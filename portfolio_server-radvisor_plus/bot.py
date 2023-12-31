import websocket, json, pandas as pd,asyncio, numpy as np, mplfinance as mpf
from binance.client import Client
from binance.enums import *
from datetime import datetime
from decouple import config
import ssl
import mysql.connector

immune_db = mysql.connector.connect(
  host=config('DB_HOST'),
  user=config('DB_USERNAME'),
  passwd=config('DB_PASSWORD'),
  database=config('DB_DATABASE')
)
#websocket.enableTrace(True)

pairs = ""
limit_sql = "3"
cur = immune_db.cursor(dictionary=True)
# get average price for x last trade
sma_d = 2
sma_l = 10
# define the difference between buy/sell price
added_val = 0.001
# contain id of sell limit order
order_id = 0
in_position = False

# init client for binance api
client = Client(config('BINANCE_K'),config('BINANCE_S'), tld='com')
##print(config('DEBUG_BINANCE'))

def save_data(id,pair):
    #print("store data start")
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    klines = client.get_historical_klines(pair, Client.KLINE_INTERVAL_1MINUTE, "24 hour ago UTC")
    sql = "INSERT INTO ohlvcs (slug, symbol_id,open,high,low,close,volume,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    for line in klines:
            val = datetime.fromtimestamp(line[0]/1000),id, line[1], line[2], line[3], line[4],line[5],datetime.fromtimestamp(line[0]/1000),datetime.fromtimestamp(line[0]/1000)
            cur.execute(sql, val)
            immune_db.commit()

    sql = "UPDATE symbols SET updated_at = %s WHERE id = %s"
    ad = (current_time,pair)
    cur.execute(sql,ad)
    #print("store data about to finish")
    immune_db.commit()
    #print(cur.rowcount, "data row executed, result 0 expected")


##print(cur.rowcount, " oorecord inserted.")
# cur = immune_db.cursor(dictionary=True)
# cur.execute("SELECT symbols.name FROM symbols RIGHT JOIN symbol_favorite ON symbol_favorite.symbol_id = symbols.id")
# pairs = cur.fetchall()
# #print(pairs)
# #print("---------------------------------------------ole")
# sql_Delete_query = "DELETE FROM ohlvcs WHERE symbol_id=%s LIMIT 1"
# pair="1"
# cur.execute(sql_Delete_query,(pair,))
# immune_db.commit()
# #print(cur.rowcount, " oorecord inserted.")
# #print("---------------------------------------------ola")

# save trade form the bot in trade.csv
async def save_trade(b_s,price,pair):
    #print("store data")
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    cur = immune_db.cursor(dictionary=True)
    sql = "INSERT INTO trades (price,symbol_id,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    val = str(current_time),pair,str(data['o']),str(data['h']),str(data['l']),str(data['v']),str(data['c']),current_time,current_time
    #print("store data : ",val)
    cur.execute(sql, val)
    immune_db.commit()
    cur = immune_db.cursor(dictionary=True)
    #print(cur.rowcount, "record inserted.")
    sql = "UPDATE symbols SET updated_at = %s WHERE id = %s"
    print("updated")
    ad = (current_time,pair)
    cur.execute(sql,ad)
    #print("store data")
    immune_db.commit()
    #print(cur.rowcount, "record inserted.")


# save last candle/close in tst.csv
async def save_close(pair,data):
    rm_last("ohlvcs",pair)
    #print("store data")
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    
    cur = immune_db.cursor(dictionary=True)
    sql = "INSERT INTO ohlvcs (slug, symbol_id,open,high,low,volume,close,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    val = str(current_time),pair,str(data['o']),str(data['h']),str(data['l']),str(data['v']),str(data['c']),current_time,current_time
    #print("store data : ",val)
    cur.execute(sql, val)
    immune_db.commit()
    #print(cur.rowcount, "record inserted.")
    cur = immune_db.cursor(dictionary=True)
    sql = "UPDATE symbols SET updated_at = %s WHERE id = %s"
    ad = (current_time,pair)
    cur.execute(sql,ad)
    #print("store data")
    immune_db.commit()
    #print(cur.rowcount, "record inserted.")

# place order on binance
def order(limit,side, quantity=config('TRADE_QUANTITY'), symbol=config('TRADE_SYMBOL'),order_type=ORDER_TYPE_MARKET):
    global order_id
    try:
        # check if order has a price limit
        if limit > 0:
            # place limit order
            order = client.create_order(symbol=symbol,side=side,type=ORDER_TYPE_LIMIT,quantity=config('TRADE_QUANTITY'),price=limit,timeInForce=TIME_IN_FORCE_GTC)
        else:
            # place market order
            order = client.create_order(symbol=symbol,side=side,type=order_type, quantity=quantity)

        #print("sending order")
        #print(order)
        order_id = order['orderId']
    except Exception as e:
        #print("an exception occured - {}".format(e))
        return False
    return True

def rm_last(table,id):
    #print(table,id)
    if table == "ohlvcs":
        cur = immune_db.cursor(dictionary=True)
        cur.execute("SELECT * FROM ohlvcs WHERE symbol_id=%s",(id,))
        number_of_rows = cur.fetchall()
        #print(number_of_rows,id)
    elif table == "signals":
        cur = immune_db.cursor(dictionary=True)
        cur.execute("SELECT * FROM signals WHERE symbol_id=%s",(id,))
        number_of_rows = cur.fetchall()
        #print(number_of_rows,id)
    if(number_of_rows != None):
        #print("ouch")
        sql_Delete_query = "DELETE FROM "+table+" WHERE symbol_id=%s LIMIT 1"
        cur.execute(sql_Delete_query,(id,))
        immune_db.commit()
    else:
       # print("none")
        immune_db.commit()

    return True

def on_open(ws):
    print('opened connection')

def on_close(ws):
    print('closed connection')

def on_message(ws, message):
    global in_position,order_id,added_val,sma_d,sma_l,buy
    # retrieve last trade

    json_message = json.loads(message)  
    cur = immune_db.cursor(dictionary=True)
    candle = json_message['data']['k']
    #print(json_message)
    get_symbol = "SELECT * FROM symbols WHERE name=%s LIMIT 1"
    cur.execute(get_symbol,(json_message['data']['s'].lower(),))
    #print(cur)
    pairs = cur.fetchall()[0]
    #print("about to get every mov for symbol ",json_message['data']['s'])
    sq = "SELECT * FROM ohlvcs WHERE symbol_id = %s"
    adr = (pairs['id'],)
    cur.execute(sq, adr)
    #print("every mov for symbol loaded")
    #print("symbol ",pairs['id'])
    df = pd.DataFrame(cur.fetchall())
    df.columns = cur.column_names
    df['close']=df['close'].astype(float)
    data = df
    #print("------------candle---------------")
    #print(candle)
    #print("----------candle-end------------")
    # calculate moving average
    sma = data['close'][-sma_d:].mean()
    sma_long = data['close'][-sma_l:].mean()
    # retrieve last close price
    close = float(candle['c'])

    sql_b = "SELECT * FROM trades WHERE symbol_id = %s ORDER BY id DESC LIMIT 1"
    valb = (pairs['id'],)
    cur.execute(sql_b,valb)
    trades = cur.fetchall()
    if(cur.rowcount != 0):
        if(trades[0]['side'] == "sell"):
            buy = True
            #print("looking to buy")
        else:
            buy = False
            #print("looking to sell")
    else:
        #print("looking to buy")
        buy = True
    if buy:
        if close > sma and close < sma_long:
            print("buy")
            cur = immune_db.cursor(dictionary=True)
            sql_o = "INSERT INTO trades (price,side,symbol_id,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            vale = (close,"buy",pairs['id'],"20",last,last)
            cur.execute(sql_o, vale)
            immune_db.commit()
            #print(cur.rowcount, " oorecord inserted.")
        else:
            rm_last("signals",pairs['id'])
            cur = immune_db.cursor(dictionary=True)
            sql_o = "INSERT INTO signals (msg,symbol_id,created_at,updated_at) VALUES (%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            l = "Automatic buy if current price ("+str(close)+") is lower than long avg("+str(sma_long)+") and higher than short avg : "+str(sma)
            vale = (l,pairs['id'],last,last)
            cur.execute(sql_o, vale)
            immune_db.commit()
            #print(cur.rowcount, " oorecord inserted.")
        
    else:
        #print("selll1",close,trades[0]['price'])
        if close < sma and close > sma_long and float(close) > float(trades[0]['price']):
            cur = immune_db.cursor(dictionary=True)
            sql_o = "INSERT INTO trades (price,side,symbol_id,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            vale = (close,"sell",pairs['id'],"20",last,last)
            #print(vale)
            cur.execute(sql_o, vale)
            immune_db.commit()
            #print("WONT SELl------------------------------")
            #print(cur.rowcount, " oorecord inserted.")
        else:
            rm_last("signals",pairs['id'])
            cur = immune_db.cursor(dictionary=True)
            sql_o = "INSERT INTO signals (msg,symbol_id,created_at,updated_at) VALUES (%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            l = "Automatic sell if current price ("+str(close)+") is bigger than long avg("+str(sma_long)+") and lower than short avg : "+str(sma)
            vale = (l,pairs['id'],last,last)
            cur.execute(sql_o, vale)
            immune_db.commit()
            #print(cur.rowcount, " oorecord inserted.")
    is_candle_closed = candle['x']
    if is_candle_closed:
        asyncio.run(save_close(pairs['id'],candle))
    print("end")

if config('DEBUG_BINANCE') == False:
    SOCKET = "wss://testnet.binance.vision/ws/bnbusdt@kline_1m"
    client.API_URL = 'https://testnet.binance.vision/api'
else:
    cur = immune_db.cursor(dictionary=True)
    cur.execute("SELECT id,name FROM symbols LIMIT "+limit_sql)
    socket_with_pairs = ""
    pairs = cur.fetchall()
    symb_fetched = False
    if cur.rowcount == 0:
        #print("fetch all pairs and insert them in db")
        #fetch all pairs and insert them in db
        exchange_info = client.get_exchange_info()
        cur = immune_db.cursor(dictionary=True)
        sql = "INSERT INTO symbols (name,graph,created_at,updated_at) VALUES (%s,%s,%s,%s)"
        limit = int(limit_sql)
        fetched = 0
        symb_fetched=True
        for s in exchange_info['symbols']:
            if fetched<limit:
                if("USDT" in s['quoteAsset']) and (s['status']=='TRADING'):
                    print(s)
                    fetched+=1
                    last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                    val = (s['symbol'],"/img/"+s['symbol']+".webp",last,last)
                    cur.execute(sql, val)
                    immune_db.commit()
        cur = immune_db.cursor(dictionary=True)
        cur.execute("SELECT id,name FROM symbols LIMIT "+limit_sql)
        pairs = cur.fetchall()
    socket_with_pairs+=pairs[0]['name'].lower()
    for x in pairs:
        if(symb_fetched):
            cur = immune_db.cursor(dictionary=True)
            save_data(x['id'],x['name'])
            sq = "SELECT * FROM ohlvcs WHERE symbol_id = %s"
            adr = (x['id'],)
            cur.execute(sq, adr)
            #print("ok")
            df = pd.DataFrame(cur.fetchall())
            df.columns = cur.column_names
            data = df
        socket_with_pairs+= "/"+x['name'].lower()+"@kline_1m"
    SOCKET = "wss://stream.binance.com/stream?streams="+socket_with_pairs

print("dame")
ws = websocket.WebSocketApp(SOCKET, on_open=on_open, on_close=on_close, on_message=on_message)
ws.run_forever(sslopt={"cert_reqs": ssl.CERT_NONE})

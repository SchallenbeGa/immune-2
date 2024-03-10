import websocket, json, pandas as pd, asyncio,mysql.connector,requests
from datetime import datetime, timedelta
from binance.helpers import round_step_size
from binance.client import Client
from binance.enums import *
from helper.bot_tweet import *
from strategy.signal import *
from decouple import config

immune_db = mysql.connector.connect(
  host=config('DB_HOST'),
  user=config('DB_USERNAME'),
  passwd=config('DB_PASSWORD'),
  database=config('DB_DATABASE')
)
# WIP : time to cancel order
# EXPIRE = False
# EXPIRE_DATE = 1660

# init client for binance api
client = Client(config('BINANCE_K'),config('BINANCE_S'), tld='com')

def save_data_n(id,pair,Client):
    #print("store data start")
    cur = immune_db.cursor(dictionary=True)
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    if config('FUTURE'): 
        klines = client.futures_historical_klines(pair, Client.KLINE_INTERVAL_1MINUTE, "30 minutes ago UTC")
    else:
        klines = client.get_historical_klines(pair, Client.KLINE_INTERVAL_1MINUTE, "1 hour ago UTC")
    sql = "INSERT INTO ohlvcs (slug, symbol_id,open,high,low,close,volume,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    for line in klines:
            val = datetime.fromtimestamp(line[0]/1000),id, line[1], line[2], line[3], line[4],line[5],datetime.fromtimestamp(line[0]/1000),datetime.fromtimestamp(line[0]/1000)
            cur.execute(sql, val)
            immune_db.commit()

    sql = "UPDATE symbols SET updated_at = %s WHERE id = %s"
    ad = (current_time,pair)
    cur.execute(sql,ad)
    immune_db.commit()

# place order on binance
def order(pair_id,pair,limit,side):
    try:
        # place limit order
        if config('FUTURE'):
            client.futures_change_leverage(symbol=pair, leverage=config('FUTURE_LEVERAGE'))
            order = client.futures_create_order(
                symbol=pair,
                side=side.upper(),
                type=FUTURE_ORDER_TYPE_LIMIT,
                quantity=config('QUANTITY'),
                price=limit,
                timeInForce=TIME_IN_FORCE_GTC)
            #print("lol")
        else: # SPOT BUY/SELL LIMIT
            print("spot")
            order = client.create_order(
                symbol=pair,
                side=side.upper(),
                type=ORDER_TYPE_LIMIT,
                quantity=config('QUANTITY'),
                price=limit,
                timeInForce=TIME_IN_FORCE_GTC)
    except Exception as e:
        print("an exception occured - {}".format(e))
        return False
   #print("sending order")
   #print(order)
    order_id = order['orderId']
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    cur = immune_db.cursor(dictionary=True)
    sql = "INSERT INTO orders (order_id,filled,price,symbol_id,side,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s)"
    val = order_id,"false",order['price'],pair_id,side,config('QUANTITY'),current_time,current_time
    #print("store data : ",val)
    cur.execute(sql, val)
    immune_db.commit()
    return True

def smart_order():
    #check if no order and change coin
   #print("smart order")
    sql_b = "SELECT symbols.id,orders.filled FROM orders LEFT JOIN symbols ON orders.symbol_id = symbols.id   WHERE orders.filled = 'false'"
    cur.execute(sql_b)
   #print("sdwadawd")
    number_of_rows = cur.fetchall()
   #print("herreeee")
        #print(number_of_rows,id)
    
   #print("herreeee")
    return cur.rowcount

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
       ##print("none")
        immune_db.commit()

    return True

async def save_close(pair,data):
    rm_last("ohlvcs",pair)
    print("store data")
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    
    cur = immune_db.cursor(dictionary=True)
    sql = "INSERT INTO ohlvcs (slug, symbol_id,open,high,low,volume,close,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    val = str(current_time),pair,str(data['o']),str(data['h']),str(data['l']),str(data['v']),str(data['c']),current_time,current_time
    print("store data : ",val)
    cur.execute(sql, val)
    immune_db.commit()
    print(cur.rowcount, "record inserted.")
    cur = immune_db.cursor(dictionary=True)
    sql = "UPDATE symbols SET updated_at = %s WHERE id = %s"
    ad = (current_time,pair)
    cur.execute(sql,ad)
    print("store data")
    immune_db.commit()
    print(cur.rowcount, "record inserted.")

def is_order_filled(symbol_id,symbol_k):
    cur = immune_db.cursor(dictionary=True)
    sql_b = "SELECT * FROM orders WHERE symbol_id = %s and filled = 'false' ORDER BY id DESC LIMIT 1"
    valb = (symbol_id,)
    cur.execute(sql_b,valb)
    trades = cur.fetchall()
   #print(trades)
    order_id_x=0
    if(cur.rowcount == 0):
        return True
    else:
        print(trades[0]['order_id'])
        order_id_x = trades[0]['order_id']
        if(trades[0]['side']=="buy"):
            Date1 = trades[0]['created_at']
            Date2 = datetime.now()
            if (Date2 - Date1)>= timedelta(minutes=3) :
                print("buy order opened more than five minute ago")
                if(Date2 - Date1)>= timedelta(minutes=20):
                    sql = "UPDATE orders SET filled = %s WHERE order_id = %s"
                    ad = ("canceled",order_id_x)
                    cur.execute(sql,ad)
                    #print("store data about to finish")
                    immune_db.commit()
                    return True
                else:
                    try:
                        result = client.futures_cancel_order(
                        symbol=symbol_k,
                        orderId=order_id_x)
                    except Exception as e:
                        print("an exception occured - {}".format(e))
               
            
        print("order here")
        
    print(order_id_x)
    if not order_id_x == 0:
        if config('FUTURE'):
            print("search to found")
            sorder = client.futures_get_order(symbol=symbol_k,orderId=order_id_x)
        else:
            sorder = client.get_order(symbol=symbol_k,orderId=order_id_x)
        # check if order is filled
        print(sorder)
        if (sorder['status'] == 'FILLED') | (sorder['status'] == 'CANCELED') :
            if (sorder['status'] == 'FILLED'):
               #print("hwwwww")
                cur = immune_db.cursor(dictionary=True)
                sql_o = "INSERT INTO trades (price,side,symbol_id,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s)"
               #print(sql_o)
                last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                valew = sorder['price'],sorder['side'],symbol_id,config('QUANTITY'),last,last
               #print(valew)
                cur.execute(sql_o, valew)
                immune_db.commit()
               #print("wdwdwwd")
                cur = immune_db.cursor(dictionary=True)
                sql = "UPDATE orders SET filled = %s WHERE order_id = %s"
                ad = ("true",order_id_x)
                cur.execute(sql,ad)
                #print("store data about to finish")
                immune_db.commit()
                if(sorder['side']== "BUY"):
                    buy_price = float(sorder['price'])
                    margin = buy_price + buy_price/1000 
                    print("this would be the price : "+str(margin))
                    tickf = float(client.get_symbol_info(symbol_k)['filters'][0]["tickSize"])
                    tickSize_limit = round_step_size(
                        margin,
                        tickf)
                    order_limit = order(symbol_id,symbol_k,tickSize_limit,"sell")
               

                # if config('TWEET') : 
                if sorder['side'] == "SELL":
                    cur = immune_db.cursor(dictionary=True)
                    sql_b = "SELECT * FROM orders WHERE symbol_id = %s AND filled = 'true' ORDER BY id DESC"
                    valb = (symbol_id,)
                    cur.execute(sql_b,valb)
                    trades = cur.fetchall()
                    #print(trades)
                    profit = float(trades[0]['price']) - float(trades[1]['price'])
                    t = symbol_k+"\nstarted : " + trades[1]['created_at'].strftime("%Y-%m-%d %H:%M:%S")+"\nstoped : " + trades[0]['created_at'].strftime("%Y-%m-%d %H:%M:%S")+"\ncoins generated : "+str(profit)
                    print(t)
                    #asyncio.run(post_twet(t))
                    requests.post("https://ntfy.sh/gabriel0alert-00",
                        data=t,
                        headers={ "Tags": "moneybag" })
                   
            else:
                sql = "UPDATE orders SET filled = %s WHERE order_id = %s"
                ad = ("canceled",order_id_x)
                cur.execute(sql,ad)
                #print("store data about to finish")
                immune_db.commit()
            return True
    return False

# run save older data 

def on_open(ws):
   print('opened connection')

def on_close(ws):
   print('closed connection')

async def on_message(symbol):
    
    cur = immune_db.cursor(dictionary=True)
    get_symbol = "SELECT * FROM symbols WHERE name=%s LIMIT 1"
    cur.execute(get_symbol,(symbol,))
    #print(cur)
    pairs = cur.fetchall()[0]

    print("about to get every mov for symbol ",symbol)
    sq = "SELECT * FROM ohlvcs WHERE symbol_id = %s"
    adr = (pairs['id'],)
    cur.execute(sq, adr)
    df = pd.DataFrame(cur.fetchall())
    df.columns = cur.column_names
    df['close']=df['close'].astype(float)
    data = df
    close = data["close"].iloc[-1]
    print("strategy : " + str(config('STRATEGY_NAME')))
    print("current price :" + str(close))
    # si il n'y a pas d'ordre en cours 
    if (is_order_filled(pairs['id'],pairs['name'])): # todo : demix
        cur = immune_db.cursor(dictionary=True)
        sql_b = "SELECT * FROM trades WHERE symbol_id = %s ORDER BY id DESC LIMIT 1"
        valb = (pairs['id'],)
        cur.execute(sql_b,valb)
        trades = cur.fetchall()
        #print(trades)
        if(cur.rowcount != 0):
            if(trades[0]['side'] == "SELL"):
                buy = True
                side="buy"
            else:
                buy = False
                side="sell"
        else:
            buy = True
            side="buy"
                
        r_price = close-(float(config('MARGIN'))*0.2)

        if (signal(data,close,client,buy,pairs['name'])) :
            tickf = float(client.get_symbol_info(pairs['name'])['filters'][0]["tickSize"])
            tickSize_limit = round_step_size(
                r_price,
                tickf)

            if buy:
                if (smart_order()<=5):
                    order_limit = order(pairs['id'],pairs['name'],tickSize_limit,side)
                else:
                    print("maximum unfilled order reached")
           

        else:
           print("not good")
    else:
       print("wait for order to get filled")
    print("order done")
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M")
    current_time_obj = datetime.strptime(current_time+":00","%Y-%m-%d %H:%M:%S")
    close_obj_o=data["created_at"].iloc[-1]
    if (close_obj_o<current_time_obj):
        sql_Delete_query = "DELETE FROM ohlvcs WHERE symbol_id=%s"
        cur.execute(sql_Delete_query,(pairs['id'],))
        immune_db.commit()
        save_data_n(pairs['id'],pairs['name'],client)
    else:
        print("no need to save")
    print("#################")

limit_sql = 5
autosymbol = True
cur = immune_db.cursor(dictionary=True)
cur.execute("SELECT id,name FROM symbols")
socket_with_pairs = ""
pairs = cur.fetchall()
print(pairs)
print(client.get_symbol_info('XRPUSDT'))
if(cur.rowcount == 1):
    socket_with_pairs+= pairs[0]['name'].lower()+"@kline_1m"
    sq = "SELECT * FROM ohlvcs WHERE symbol_id = %s"
    adr = (pairs[0]['id'],)
    cur.execute(sq, adr)
    if cur.rowcount<=0:
        res = cur.fetchall()
        save_data_n(pairs[0]['id'],pairs[0]['name'],client)
       #print("saved")
    else:
        res = cur.fetchall()
       #print("daata already exist!!")
    
else:
    if cur.rowcount == 0:
       #print("no crypto added")
        if autosymbol:
            exchange_info = client.get_all_tickers()
            cur = immune_db.cursor(dictionary=True)
            sql = "INSERT INTO symbols (name,created_at,updated_at) VALUES (%s,%s,%s)"
            fetched = 0
            symb_fetched=True
            for s in exchange_info:
                if fetched<limit_sql:
                    if(float(s['price'])>0.30) and (float(s['price'])<0.95) :
                        symbol_info = client.get_symbol_info(s['symbol'])
                        if('TRD_GRP_006' in symbol_info["permissions"]) and (symbol_info['quoteAsset'] == "USDT"):
                           #print(symbol_info)
                            if(fetched==0):
                                socket_with_pairs+= s['symbol'].lower()
                            fetched+=1
                            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                            val = (s['symbol'],last,last)
                            cur.execute(sql, val)
                            immune_db.commit()
            cur = immune_db.cursor(dictionary=True)
            cur.execute("SELECT id,name FROM symbols")
            pairs = cur.fetchall()
           #print(pairs)
    c=0
    print(pairs)
    while True:
        for x in pairs:
            sql_Delete_query = "DELETE FROM ohlvcs WHERE symbol_id=%s"
            cur.execute(sql_Delete_query,(x['id'],))
            immune_db.commit()
            save_data_n(x['id'],x['name'],client)
            asyncio.run(on_message(x['name']))
        
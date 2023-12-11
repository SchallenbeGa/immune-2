import websocket, json, aiofiles,pandas as pd,asyncio,numpy as np,mplfinance as mpf
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
print(config('DEBUG_BINANCE'))

def save_data(id,pair):
    print("store data")
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    klines = client.get_historical_klines(pair, Client.KLINE_INTERVAL_1MINUTE, "1 hour ago UTC")
    sql = "INSERT INTO ohlvcs (slug, symbol_id,open,high,low,close,volume,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    for line in klines:
            val = datetime.fromtimestamp(line[0]/1000),id, line[1], line[2], line[3], line[4],line[5],datetime.fromtimestamp(line[0]/1000),datetime.fromtimestamp(line[0]/1000)
            cur.execute(sql, val)
            immune_db.commit()

    sql = "UPDATE symbols SET updated_at = %s WHERE id = %s"
    ad = (current_time,pair)
    cur.execute(sql,ad)
    print("store data")
    immune_db.commit()
    print(cur.rowcount, "record inserted.")


if config('DEBUG_BINANCE') == False:
    print(config('DEBUG_BINANCE'))
    # trace websocket exchange
    
    # use testnet api
    SOCKET = "wss://testnet.binance.vision/ws/bnbusdt@kline_1m"
    client.API_URL = 'https://testnet.binance.vision/api'
else:
    cur = immune_db.cursor(dictionary=True)
    cur.execute("SELECT id,name FROM symbols limit 3")
    socket_with_pairs = ""
    pairs = cur.fetchall()
    fetched = False
    if cur.rowcount == 0:
        print("fetch all pairs and insert them in db")
        #fetch all pairs and insert them in db
        exchange_info = client.get_exchange_info()
        sql = "INSERT INTO symbols (name,graph,created_at,updated_at) VALUES (%s,%s,%s,%s)"
        fetched=True
        for s in exchange_info['symbols']:
            if("USDT" in s['quoteAsset']) and (s['status']=='TRADING'):
                print(s)
                last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                val = (s['symbol'],"/img/"+s['symbol']+".png",last,last)
                cur.execute(sql, val)
                immune_db.commit()
        cur.execute("SELECT id,name FROM symbols limit 3")
        pairs = cur.fetchall()
    for x in pairs:
        print(x)
        if(fetched):
            save_data(x['id'],x['name'])
        socket_with_pairs+= "/"+x['name'].lower()+"@kline_1m"
    SOCKET = "wss://stream.binance.com/stream?streams=bnbusdt@kline_1m"+socket_with_pairs

sql = "INSERT INTO signals (msg,symbol_id,created_at,updated_at) VALUES (%s,%s,%s,%s)"
last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
id = "9"
l = "Good short signal (awd), if current price is lower than long avg(awd) and higher than short avg : "
val = (l,id,last,last)
cur.execute(sql, val)
immune_db.commit()
print(cur.rowcount, " oorecord inserted.")



# create/save graph with buy/sell indicators (& post on twitter) format PNG
async def twet_graph(data,tweet_content,fav,pair,pair_name):
    global api,sma_d
    print("ole")
    buys = []
    sells = []

    one_sell = False
    one_buy = False

    print("start graph")
    data['open']=data['open'].astype(float)
    data['close']=data['close'].astype(float)
    data['high']=data['high'].astype(float)
    data['low']=data['low'].astype(float)
    data['volume']=data['volume'].astype(float)
    # retrieve chart data
    data.index = pd.to_datetime(data['created_at'],format="%Y-%m-%d %H:%M:%S")


    sq = "SELECT * FROM trades WHERE symbol_id = %s"
    adr = (pair,)
    cur.execute(sq, adr)
    print("ok")
    trade = pd.DataFrame(cur.fetchall())
    if cur.rowcount > 0 :
        trade.columns = cur.column_names
        trade.index = pd.to_datetime(trade['created_at'],format="%Y-%m-%d %H:%M:%S")
        trade['price'] = trade['price'].astype(float)
    #print(trade,data)
    # create custom style for graph
    s  = mpf.make_mpf_style(
        base_mpf_style="yahoo",
        facecolor="#282828",
        gridcolor="#212121",
        rc={'xtick.color':'#f8f8f8','ytick.color':'#f8f8f8','axes.labelcolor':'#f8f8f8'})
    # check if there is at least 1 trade
    print(data)
    if (len(trade)>0):
        for x in range(len(data)):
            n = False
            inCandleTrade = False
            for y in range(len(trade)):
                if trade.index.array[y].hour < data.index.array[0].hour: #remove old trade
                    print(trade.index.array[y].hour)
                    print(data.index.array[0].hour)
                else:
                    print("here")
                    if (data.index.array[x].minute == trade.index.array[y].minute) & (data.index.array[x].hour == trade.index.array[y].hour) :
                        if(trade['side'][y]=="buy"):
                            print("okay")
                            if not inCandleTrade:
                                buys.append(trade['price'][y])
                                one_buy = True
                                inCandleTrade = True
                            else:
                                print("buy in same candle "+str(y))
                            n = True
                        else:
                            one_sell=True
                            sells.append(trade['price'][y])
                            n = True
                    if len(buys)>len(sells):
                        sells.append(np.nan)
                    elif len(buys)<len(sells):
                        buys.append(np.nan)
            if not n:
                buys.append(np.nan)
                sells.append(np.nan)
        print(len(data),len(buys),len(sells))
        print(buys)
        print(sells)
        if one_sell & one_buy :
            apd = [
                mpf.make_addplot(buys, scatter=True, markersize=120, marker=r'^', color='green'),
                mpf.make_addplot(sells, scatter=True, markersize=120, marker=r'v', color='red')
            ]
        elif one_sell:
            print("one sell")
            apd = [mpf.make_addplot(sells, scatter=True, markersize=120, marker=r'v', color='red')]
        elif one_buy :
            print("one buy")
            apd = [mpf.make_addplot(buys, scatter=True, markersize=120, marker=r'^', color='green')]
        print("repartition done")
        data.rename(columns={"open": "Open","close":"Close","high":"High","low":"Low","volume":"Volume"},inplace=True)
        data.index.name = 'Date'
        data.drop(columns=['created_at','symbol_id','updated_at','slug','id'],inplace=True)
        # cols = ['Date', 'Open', 'High', 'Low','Close','Volume']
        # data = data[cols]
        print(data)
        fig,ax = mpf.plot(
            data,
            addplot=apd,
            type='candle',
            volume=True,
            style=s,
            mav=(sma_d,sma_l),
            figscale=1,
            figratio=(20,10),
            datetime_format="%d %H:%M:%S",
            xrotation=0,
            returnfig=True)
    else: 
        print("empty graph") 
        data.rename(columns={"open": "Open","close":"Close","high":"High","low":"Low","volume":"Volume"},inplace=True)
        data.index.name = 'Date'
        data.drop(columns=['created_at','symbol_id','updated_at','slug','id'],inplace=True)
        # cols = [ 'Open', 'High', 'Low','Close','Volume']
        # data = data[cols]
        print(data)
        fig,ax = mpf.plot(
            data,
            type='candle',
            volume=True,
            style=s,
            figscale=1,
            mav=(sma_d,sma_l),
            figratio=(20,10),
            datetime_format="%d %H:%M:%S",
            xrotation=0,
            returnfig=True)
        print("pain generating")
    # save graph in png 
    print("about to save") 
    fig.savefig('../public/img/'+pair_name+'.png',facecolor='#282828')
    print("savegraph")

# save trade form the bot in trade.csv
async def save_trade(b_s,price,pair):
    print("store data")
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    cur = immune_db.cursor(dictionary=True)
    sql = "INSERT INTO trades (price,symbol_id,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    val = str(current_time),pair,str(data['o']),str(data['h']),str(data['l']),str(data['v']),str(data['c']),current_time,current_time
    print("store data : ",val)
    cur.execute(sql, val)
    immune_db.commit()
    print(cur.rowcount, "record inserted.")
    sql = "UPDATE symbols SET updated_at = %s WHERE id = %s"
    ad = (current_time,pair)
    cur.execute(sql,ad)
    print("store data")
    immune_db.commit()
    print(cur.rowcount, "record inserted.")


    async with aiofiles.open('trade.csv', mode='r') as f:
        contents = await f.read()
        current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        contents = contents+str(str(current_time)+","+str(b_s)+","+str(price)+","+str(config('TRADE_QUANTITY'))+"\n")
    async with aiofiles.open('trade.csv', mode='w') as f:
        await f.write(contents)

# save older candle in tst.csv


# save last candle/close in tst.csv
async def save_close(pair,data):
    print("store data")
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    cur = immune_db.cursor(dictionary=True)
    sql = "INSERT INTO ohlvcs (slug, symbol_id,open,high,low,volume,close,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    val = str(current_time),pair,str(data['o']),str(data['h']),str(data['l']),str(data['v']),str(data['c']),current_time,current_time
    print("store data : ",val)
    cur.execute(sql, val)
    immune_db.commit()
    print(cur.rowcount, "record inserted.")
    sql = "UPDATE symbols SET updated_at = %s WHERE id = %s"
    ad = (current_time,pair)
    cur.execute(sql,ad)
    print("store data")
    immune_db.commit()
    print(cur.rowcount, "record inserted.")

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

        print("sending order")
        print(order)
        order_id = order['orderId']
    except Exception as e:
        print("an exception occured - {}".format(e))
        return False
    return True

def on_open(ws):
    print('opened connection')

def on_close(ws):
    print('closed connection')

def on_message(ws, message):
    global in_position,order_id,added_val,sma_d,sma_l,buy
    # retrieve last trade

    json_message = json.loads(message)  
    print("-------------")

    candle = json_message['data']['k']
    # is_candle_closed = candle['x']
    # if is_candle_closed:
    #     asyncio.run(twet_graph(":)",False))

    #print("about to save : ",candle)  
 
    id = next(item for item in pairs if item["name"] == json_message['data']['s'])['id']

    print("----------------------")
    print("next")
    sq = "SELECT * FROM ohlvcs WHERE symbol_id = %s"
    adr = (id,)
    cur.execute(sq, adr)
    print("ok")
    print(id)
    df = pd.DataFrame(cur.fetchall())
    df.columns = cur.column_names
    df['close']=df['close'].astype(float)
    data = df
    print("-----------------------------")
    print(candle)
    print("--------------hey--------------")
    # calculate moving average
    sma = data['close'][-sma_d:].mean()
    print(sma)
    sma_long = data['close'][-sma_l:].mean()
    print(sma_long)
    print("calc ok")
    # retrieve last close price
    close = float(candle['c'])

   

    

    print(json_message['data']['s']+" - start to trade -")
    sql_b = "SELECT * FROM trades WHERE symbol_id = %s ORDER BY id DESC LIMIT 1"
    valb = (id,)
    cur.execute(sql_b,valb)
    trades = cur.fetchall()
    print(trades)
    if(cur.rowcount != 0):
        if(trades[0]['side'] == "sell"):
            buy = True
        else:
            buy = False
    else:
        buy = True
    if buy:
        if close > sma and close < sma_long:
            sql_o = "INSERT INTO trades (price,side,symbol_id,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            vale = (close,"buy",id,"20",last,last)
            cur.execute(sql_o, vale)
            immune_db.commit()
            print(cur.rowcount, " oorecord inserted.")
        else:
            print(json_message['data']['s']+" - current price :",close)
            sql_o = "INSERT INTO signals (msg,symbol_id,created_at,updated_at) VALUES (%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            l = "Automatic buy if current price ("+str(close)+") is lower than long avg("+str(sma_long)+") and higher than short avg : "+str(sma)
            vale = (l,id,last,last)
            cur.execute(sql_o, vale)
            immune_db.commit()
            print(cur.rowcount, " oorecord inserted.")
        
    else:
        print("selll1",close,trades[0]['price'])
        if close < sma and close > sma_long and float(close) > float(trades[0]['price']):
            print("sell4")
            sql_o = "INSERT INTO trades (price,side,symbol_id,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            vale = (close,"sell",id,"20",last,last)
            print(vale)
            cur.execute(sql_o, vale)
            immune_db.commit()
            print("WONT SELl------------------------------")
            print(cur.rowcount, " oorecord inserted.")
        else:
            print("waiting to sell")
            sql_o = "INSERT INTO signals (msg,symbol_id,created_at,updated_at) VALUES (%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            l = "Automatic sell if current price ("+str(close)+") is bigger than long avg("+str(sma_long)+") and lower than short avg : "+str(sma)
            vale = (l,id,last,last)
            cur.execute(sql_o, vale)
            immune_db.commit()
            print(cur.rowcount, " oorecord inserted.")
   

    is_candle_closed = candle['x']
    if is_candle_closed:
        print("send to graph")
        asyncio.run(save_close(id,candle))
        asyncio.run(twet_graph(data,"test",True,id,json_message['data']['s']))

ws = websocket.WebSocketApp(SOCKET, on_open=on_open, on_close=on_close, on_message=on_message)
ws.run_forever(sslopt={"cert_reqs": ssl.CERT_NONE})

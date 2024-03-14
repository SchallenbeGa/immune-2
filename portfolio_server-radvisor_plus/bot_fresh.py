import pandas as pd, mysql.connector,requests,asyncio
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
# init client for binance api
client = Client(config('BINANCE_K'),config('BINANCE_S'), tld='com')

# place order on binance
def order(pair_id,pair,limit,side,quantity):
    try:
        client.futures_change_leverage(symbol=pair, leverage=config('FUTURE_LEVERAGE'))
        order = client.futures_create_order(
            symbol=pair,
            side=side.upper(),
            type=FUTURE_ORDER_TYPE_LIMIT,
            quantity=quantity,
            price=limit,
            timeInForce=TIME_IN_FORCE_GTC)
    except Exception as e:
        print("an exception occured - {}".format(e))
        return False
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    cur = immune_db.cursor(dictionary=True)
    sql = "INSERT INTO orders (order_id,filled,price,symbol_id,side,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s)"
    val = order['orderId'],"false",order['price'],pair_id,side,quantity,current_time,current_time
    #print("store data : ",val)
    cur.execute(sql, val)
    immune_db.commit()
    return True

def smart_order():
    sql_b = "SELECT symbols.id,orders.filled FROM orders LEFT JOIN symbols ON orders.symbol_id = symbols.id   WHERE orders.filled = 'false'"
    cur.execute(sql_b)
    return cur.rowcount

def rm_last(table,id):
    if table == "ohlvcs":
        cur = immune_db.cursor(dictionary=True)
        cur.execute("SELECT * FROM ohlvcs WHERE symbol_id=%s",(id,))
        number_of_rows = cur.fetchall()
    elif table == "signals":
        cur = immune_db.cursor(dictionary=True)
        cur.execute("SELECT * FROM signals WHERE symbol_id=%s",(id,))
        number_of_rows = cur.fetchall()
    if(number_of_rows != None):
        sql_Delete_query = "DELETE FROM "+table+" WHERE symbol_id=%s LIMIT 1"
        cur.execute(sql_Delete_query,(id,))
        immune_db.commit()
    else:
        immune_db.commit()

    return True

def is_order_filled(symbol_id,symbol_k):
    cur = immune_db.cursor(dictionary=True)
    sql_b = "SELECT * FROM orders WHERE symbol_id = %s and filled = 'false' ORDER BY id DESC LIMIT 1"
    valb = (symbol_id,)
    cur.execute(sql_b,valb)
    trades = cur.fetchall()
    
    order_id_x=0
    if(cur.rowcount == 0):
        return True
    else:
        print(trades[0]['order_id'])
        order_id_x = trades[0]['order_id']
        if(trades[0]['side']=="buy"):
            Date1 = trades[0]['created_at']
            Date2 = datetime.now()
            if (Date2 - Date1)>= timedelta(minutes=1) :
                print("buy order opened more than five minute ago")
                if(Date2 - Date1)>= timedelta(minutes=20):
                    sql = "UPDATE orders SET filled = %s WHERE order_id = %s"
                    ad = ("canceled",order_id_x)
                    cur.execute(sql,ad)
                    immune_db.commit()
                    return True
                else:
                    try:
                        result = client.futures_cancel_order(
                        symbol=symbol_k,
                        orderId=order_id_x)
                    except Exception as e:
                        print("an exception occured - {}".format(e))
                        
    if order_id_x == 0:
        return False
    else:
        try:
            order = client.futures_get_order(symbol=symbol_k,orderId=order_id_x)
        except Exception as e:
            print("an exception occured - {}".format(e))
            return False

        if (order['status'] == 'FILLED'):
            cur = immune_db.cursor(dictionary=True)
            sql_o = "INSERT INTO trades (price,side,symbol_id,quantity,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s)"
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            valew = order['price'],order['side'],symbol_id,config('QUANTITY'),last,last
            cur.execute(sql_o, valew)
            immune_db.commit()
            cur = immune_db.cursor(dictionary=True)
            sql = "UPDATE orders SET filled = %s WHERE order_id = %s"
            ad = ("true",order_id_x)
            cur.execute(sql,ad)
            immune_db.commit()
            if(order['side']== "BUY"):
                buy_price = float(order['price'])
                margin = buy_price + buy_price/1000 
                print("this would be the price : "+str(margin))
                tickf = float(client.get_symbol_info(symbol_k)['filters'][0]["tickSize"])
                tickSize_limit = round_step_size(
                    margin,
                    tickf)
                quantity = config('QUANTITY')/tickSize_limit
                order_limit = order(symbol_id,symbol_k,tickSize_limit,"sell",quantity)
            else:
                cur = immune_db.cursor(dictionary=True)
                sql_b = "SELECT * FROM orders WHERE symbol_id = %s AND filled = 'true' ORDER BY id DESC"
                valb = (symbol_id,)
                cur.execute(sql_b,valb)
                trades = cur.fetchall()
                profit = (float(trades[0]['price']) - float(trades[1]['price']))*100
                t = symbol_k+"\nstarted : " + trades[1]['created_at'].strftime("%Y-%m-%d %H:%M:%S")+"\nstoped : " + trades[0]['created_at'].strftime("%Y-%m-%d %H:%M:%S")+"\ncoins generated : "+str(profit)
                requests.post(config('NOTIF'),
                    data=t,
                    headers={ "Tags": "moneybag" })   
        else:
            sql = "UPDATE orders SET filled = %s WHERE order_id = %s"
            ad = ("canceled",order_id_x)
            cur.execute(sql,ad)
            immune_db.commit()
        return True

def on_message(symbol,data):
    
    cur = immune_db.cursor(dictionary=True)
    get_symbol = "SELECT * FROM symbols WHERE name=%s LIMIT 1"
    cur.execute(get_symbol,(symbol,))
    #print(cur)
    pairs = cur.fetchall()[0]
    print(pairs)
    close = data["close"].iloc[-1]
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

        # if (signal(data,close,client,buy,pairs['name'])) :
        #     tickf = float(client.get_symbol_info(pairs['name'])['filters'][0]["tickSize"])
        #     tickSize_limit = round_step_size(
        #         r_price,
        #         tickf)

        #     if buy:
        #         if (smart_order()<=5):
        #           quantity = config('QUANTITY')/tickSize_limit
        #             order_limit = order(pairs['id'],pairs['name'],tickSize_limit,side,quantity)
        #         else:
        #             print("maximum unfilled order reached")
           

        # else:
        #    print("not good")
    else:
       print("wait for order to get filled")
    print("order done")
    print("#################")

cur = immune_db.cursor(dictionary=True)
get_all_symbol = "SELECT * FROM symbols"
cur.execute(get_all_symbol)
pairs = cur.fetchall()
if(cur.rowcount<=0):
    exchange_info = client.get_all_tickers()
    cur = immune_db.cursor(dictionary=True)
    sql = "INSERT INTO symbols (name,created_at,updated_at) VALUES (%s,%s,%s)"
    for s in exchange_info:
        print(s)
        symbol_info = client.get_symbol_info(s['symbol'])
        if('TRD_GRP_006' in symbol_info["permissions"]) and (symbol_info['quoteAsset'] == "USDT"):
            last = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            val = (s['symbol'],last,last)
            print(symbol_info)
            cur.execute(sql, val)
            immune_db.commit()
cur = immune_db.cursor(dictionary=True)
get_all_symbol = "SELECT * FROM symbols"
cur.execute(get_all_symbol)
pairs = cur.fetchall()
for x in pairs:
    klines = client.futures_historical_klines(x['name'], Client.KLINE_INTERVAL_1MINUTE, "5 minutes ago UTC")
    ohlc_data = [[float(kline[1]), float(kline[2]), float(kline[3]), float(kline[4])] for kline in klines]
    df = pd.DataFrame(ohlc_data, columns=['open', 'high', 'low', 'close'])
    timestamps = [datetime.fromtimestamp(int(kline[0]) / 1000) for kline in klines]
    df['Timestamp'] = timestamps
    df.set_index('Timestamp', inplace=True)
    data = df
    print(data)
    on_message(x['name'],data)
    print(pairs)
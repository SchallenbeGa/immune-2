# AUTOMATE BINANCE BUY/SELL

* warning : use at ur own risk
* take care of fees : https://www.binance.com/en/fee/schedule (login to see your fee level)
* api need ONLY listen permission

## CONFIG.PY

   ### BINANCE
    API_KEY = 'ndSdbjSmijYKsKabl4eue3nBexampleuawHro9SgRgXVctvJ0YvoozJl3'
    API_SECRET = 'c5dnUZ3sD4G1OzMFzcLUtieamplePbikmFmje8nPsUeadeeObTielDeyU'

   
   ### VAR
    PAIR = 'bnbusdt'
    PAIR_S = 'usdt'
    PAIR_B = "bnb"
    QUANTITY = '20' # SHOULD NEVER BE MORE THAN 10% OF WALLET !
   ### ENVIRONMENT
    DEBUG = True
    TESTNET = False
   ### FUTURE
    FUTURE = True
    FUTURE_LEVERAGE = '1'
    FUTURE_COIN = False (wip)
   ### STRATEGY VAR
    STRATEGY_NAME = "sma&rsi" # sma|rsi|orderbook
    RISK = 1 safest (1-3)
    PERIOD = 2 nb trade to anaylse
   ### POSITION
    MARGIN=0.0008

## COMMAND

    python3 bot.py


## IMPORT

      pip install python-binance websocket-client pandas asyncio numpy matplotlib mplfinance mysql-connector

## EXTERNAL SERVER ROUTINE (take care)

      php artisan migrate:fresh
      rm -rf public/img/*
      nohup /opt/alt/python310/bin/python3 portfolio_server-radvisor_plus/bot.py &
      

## LIBRARIES

 * https://python-binance.readthedocs.io/en/latest/index.html
 * https://pypi.org/project/websocket-client/
 * https://pypi.org/project/asyncio/
 * https://github.com/matplotlib/mplfinance
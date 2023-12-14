# AUTOMATE BINANCE BUY/SELL

* warning : use at ur own risk
* take care of fees : https://www.binance.com/en/fee/schedule (login to see your fee level)
* api need ONLY listen permission

## ENV Variable
   ### BINANCE
    API_KEY = 'ndSdbjSmijYKsKabl4eue3nBexampleuawHro9SgRgXVctvJ0YvoozJl3'
    API_SECRET = 'c5dnUZ3sD4G1OzMFzcLUtieamplePbikmFmje8nPsUeadeeObTielDeyU'

## EXTERNAL SERVER ROUTINE (take care)

      php artisan migrate:fresh
      rm -rf public/img/*
      cd portfolio_server-radvisor_plus
      nohup /opt/alt/python310/bin/python3 bot.py > my.log 2>&1 
      

## LIBRARIES

 * https://python-binance.readthedocs.io/en/latest/index.html
 * https://pypi.org/project/websocket-client/
 * https://pypi.org/project/asyncio/
 * https://github.com/matplotlib/mplfinance
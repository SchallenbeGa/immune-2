# choose/combine strategy
from decouple import config
from strategy.s_rsi import *
from strategy.s_order_book import *
from strategy.s_sma import *
from strategy.s_easy import *

multi = True # combine strategy


def signal(data,close,client,side_buy,pair):
    # multi strategy signalz
    if not side_buy : return True
    if config('STRATEGY_NAME') == "sma":
        return s_sma(data,close,config('RISK'),config('PERIOD'))
    elif config('STRATEGY_NAME') == "rsi":
        return s_rsi(data,config('RISK'),config('PERIOD'))
    elif config('STRATEGY_NAME') == "easy":
        return s_easy(data,close)
    else:
        return s_order_book(config('RISK'),client,pair.upper())

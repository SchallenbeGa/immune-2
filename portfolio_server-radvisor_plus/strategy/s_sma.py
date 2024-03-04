
# strategy sma
def s_sma(data,close,risk,period):

    if risk == 1:
        sma = data['close'][-(period*3):].mean()
        sma_long = data['close'][-(period*4):].mean()
        sma_long_x = data['close'][-100:].mean()
    elif risk == 2:
        sma = data['close'][-(period*2):].mean()
        sma_long = data['close'][-(period*3):].mean()
        sma_long_x = data['close'][-80:].mean()
    else :
        sma = data['close'][-period:].mean()
        sma_long = data['close'][-(period+1):].mean()
        sma_long_x = data['close'][-(period+3):].mean()

    print("-----------------")
    print("sma_short:"+str(sma))
    print("sma_long:"+str(sma_long))
    print("sma_long_x:"+str(sma_long_x))
    if (close > sma) & (close < sma_long) & (close < sma_long_x):
        return True
    
    return False

# strategy sma but easy
def s_easy(data,close):

    sma = data['close'][-2:].mean()
    sma_long = data['close'][-10:].mean()

    print("-----------------")
    print("sma_short:"+str(sma))
    print("sma_long:"+str(sma_long))
    l = "Automatic buy if current price ("+str(close)+") is lower than long avg("+str(sma_long)+") and higher than short avg : "+str(sma)
    print(l)
    if close > sma and close < sma_long:
        return True
    
    return False


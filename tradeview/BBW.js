//@version=3
study(title="Bollinger Bands Width", shorttitle="BBW")
length = input(20, minval=1)
src = input(close, title="Source")
mult = input(2.0, minval=0.001, maxval=50)
basis = sma(src, length)
dev = mult * stdev(src, length)
upper = basis + dev
lower = basis - dev
bbw = (upper-lower)/basis
plot(bbw, color=blue)

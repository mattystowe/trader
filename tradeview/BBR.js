//@version=3
study(title = "Bollinger Bands %B", shorttitle = "BB %B")
length = input(20, minval=1)
src = input(close, title="Source")
mult = input(2.0, minval=0.001, maxval=50)
basis = sma(src, length)
dev = mult * stdev(src, length)
upper = basis + dev
lower = basis - dev
bbr = (src - lower)/(upper - lower)
plot(bbr, color=teal)
band1 = hline(1, color=gray, linestyle=dashed)
band0 = hline(0, color=gray, linestyle=dashed)
fill(band1, band0, color=teal)

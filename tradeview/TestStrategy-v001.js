//@version=3
strategy("BBands DMI Strategy v001", overlay=true)
strategy.risk.allow_entry_in(strategy.direction.long) // There will be no short entries, only exits from long.


//Bbands
length = input(20, minval=1)
src = input(close, title="Source")
mult = input(2.0, minval=0.001, maxval=50)
basis = sma(src, length)
dev = mult * stdev(src, length)
upper = basis + dev
lower = basis - dev
plot(basis, color=red)
p1 = plot(upper, color=blue)
p2 = plot(lower, color=blue)
fill(p1, p2)

//BB%
bbr = (src - lower) / (upper - lower)


//BB Width
bbw = (upper - lower ) / basis


//DMI
len = input(14, minval=1, title="DI Length")
lensig = input(14, title="ADX Smoothing", minval=1, maxval=50)

up = change(high)
down = -change(low)
plusDM = na(up) ? na : (up > down and up > 0 ? up : 0)
minusDM = na(down) ? na : (down > up and down > 0 ? down : 0)
trur = rma(tr, len)
plus = fixnan(100 * rma(plusDM, len) / trur)
minus = fixnan(100 * rma(minusDM, len) / trur)
sum = plus + minus
adx = 100 * rma(abs(plus - minus) / (sum == 0 ? 1 : sum), lensig)

//plot(plus, color=blue, title="+DI")
//plot(minus, color=orange, title="-DI")
//plot(adx, color=red, title="ADX")

//----------------





//Condition Signals BUY
bbands_lower_crossed = crossover(src,lower) // dipped below lower band and crossed back over it.
bb_width_target = bbw >= 0.015 // bb width is worth it (offers over 1.5% gain bottom to top)
strongtrend = (adx > 20)


//Condition Signals SELL
bbands_upper_crossed = crossunder(src,upper)
close_to_upper = crossover(bbr,0.8)



if (bbands_lower_crossed)
    if (bb_width_target)
        if (strongtrend)
            strategy.entry(id="Buy", long = true)
            //strategy.exit(id="StopLoss", from_entry="Buy", limit=close+(close*0.02))


if (close_to_upper)
    strategy.close(id="Buy")

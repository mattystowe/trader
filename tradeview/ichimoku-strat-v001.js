//@version=2
// Any timeFrame ok but good on 15 minute & 60 minute , Ichimoku + Daily-Candle_cross(DT) + HULL-MA_cross + MacD combination 420 special blend
strategy("Ichimoku + Daily-Candle_X + HULL-MA_X + MacD", shorttitle="٩(̾●̮̮̃̾•̃̾)۶", overlay=true, default_qty_type=strategy.percent_of_equity, max_bars_back=720, default_qty_value=100, calc_on_order_fills= true, calc_on_every_tick=true, pyramiding=0)

// === BACKTEST RANGE ===
FromMonth = input(defval = 10, title = "From Month", minval = 1)
FromDay   = input(defval = 3, title = "From Day", minval = 1)
FromYear  = input(defval = 2017, title = "From Year", minval = 2014)
ToMonth   = input(defval = 1, title = "To Month", minval = 1)
ToDay     = input(defval = 1, title = "To Day", minval = 1)
ToYear    = input(defval = 9999, title = "To Year", minval = 2014)

keh=input(title="Double HullMA",type=integer,defval=14, minval=1)
dt = input(defval=0.0010, title="Decision Threshold (0.001)", type=float, step=0.0001)
SL = input(defval=-500.00, title="Stop Loss in $", type=float, step=1)
TP = input(defval=25000.00, title="Target Point in $", type=float, step=1)
ot=1
n2ma=2*wma(close,round(keh/2))
nma=wma(close,keh)
diff=n2ma-nma
sqn=round(sqrt(keh))
n2ma1=2*wma(close[1],round(keh/2))
nma1=wma(close[1],keh)
diff1=n2ma1-nma1
sqn1=round(sqrt(keh))
n1=wma(diff,sqn)
n2=wma(diff1,sqn)

b=n1>n2?lime:red
c=n1>n2?green:red
d=n1>n2?red:green

confidence=(security(tickerid, 'D', close)-security(tickerid, 'D', close[1]))/security(tickerid, 'D', close[1])

conversionPeriods = input(9, minval=1, title="Conversion Line Periods")
basePeriods = input(26, minval=1, title="Base Line Periods")
laggingSpan2Periods = input(52, minval=1, title="Lagging Span 2 Periods")
displacement = input(26, minval=1, title="Displacement")


donchian(len) => avg(lowest(len), highest(len))

conversionLine = donchian(conversionPeriods)
baseLine = donchian(basePeriods)
leadLine1 = avg(conversionLine, baseLine)
leadLine2 = donchian(laggingSpan2Periods)


LS=close, offset = -displacement
MACD_Length = input(9)
MACD_fastLength = input(12)
MACD_slowLength = input(26)


MACD = ema(close, MACD_fastLength) - ema(close, MACD_slowLength)
aMACD = ema(MACD, MACD_Length)
closelong = n1<n2 and close<n2 and confidence<dt or strategy.openprofit<SL or strategy.openprofit>TP
if (closelong)
    if (time < timestamp(ToYear, ToMonth, ToDay, 23, 59)) // backtest limits
        strategy.close("Long")
//closeshort = n1>n2 and close>n2 and confidence>dt or strategy.openprofit<SL or strategy.openprofit>TP
//if (closeshort)
//    strategy.close("Short")
longCondition = n1>n2 and strategy.opentrades<ot and confidence>dt and close>n2 and leadLine1>leadLine2 and open<LS and MACD>aMACD
if (longCondition)
    if (time > timestamp(FromYear, FromMonth, FromDay, 00, 00) and (time < timestamp(ToYear, ToMonth, ToDay, 23, 59))) //backtest limits
        strategy.entry("Long",strategy.long)
//shortCondition = n1<n2 and strategy.opentrades<ot and confidence<dt and close<n2 and leadLine1<leadLine2 and open>LS and MACD<aMACD
//if (shortCondition)
//    strategy.entry("Short",strategy.short)//                         /L'-,


//a1=plot(n1,color=c)
//a2=plot(n2,color=c)
//plot(cross(n1, n2) ? n1 : na, style = circles, color=b, linewidth = 4)
//plot(cross(n1, n2) ? n1 : na, style = line, color=d, linewidth = 4)
plot(conversionLine, color=#0496ff, title="Conversion Line")
plot(baseLine, color=#991515, title="Base Line")
//plot(close, offset = -displacement, color=#459915, title="Lagging Span")
p1=plot (leadLine1, offset = displacement, color=green,  title="Lead 1")
p2=plot (leadLine2, offset = displacement, color=red,  title="Lead 2")
fill(p1, p2, color = leadLine1 > leadLine2 ? green : red)
// remove the "//" from before the plot script if want to see the indicators on chart

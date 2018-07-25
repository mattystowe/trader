<?php
/*
* RinseMachineV1 Strategy - 5m Trading Bot - needs 2h data for comparison
*
* PINE SCRIPT
*@version=2
strategy("GetTrendStrategy", overlay=true)
strategy.risk.allow_entry_in(strategy.direction.long)
tim=input('120')
out1 = security(tickerid, tim, open)
out2 = security(tickerid, tim, close)
//out1 = open[160]
//out2 = close[160]
plot(out1,color=red)
plot(out2,color=green)
longCondition = crossover(security(tickerid, tim, close),security(tickerid, tim, open))
if (longCondition)
    strategy.entry("long", strategy.long)
shortCondition = crossunder(security(tickerid, tim, close),security(tickerid, tim, open))
if (shortCondition)
    strategy.entry("short", strategy.short)
*/
namespace App\Strategies;

use Log;
use \App\Activemarket;
use \App\Market;
use \App\Activestrategy;
use \App\Strategy;
use \App\Candles;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\Trader;
use App\MathUtils;

class LittleRinseV1 extends BaseStrategy
{





  function __construct(Activestrategy $activestrategy, Activemarket $activemarket, $backtest = false, $backtest_skip = 0, $backtest_pick = 0)
    {
      $this->setup();
      parent::__construct($activestrategy, $activemarket, $backtest, $backtest_skip, $backtest_pick);
    }





  ////////////////////////////////////////
  //Main Config for strategy
  //
  //
  private function setup() {


  }






  /////////////////////////////////////
  //All strategies are called to think!
  //
  //
  //
  public function think() {


    $OHLCV_Comparison_Data = $this->loadComparisonTimeFrameOHLCV('2h');

    $H2_open = last($OHLCV_Comparison_Data)['open'];
    $H2_close = last($OHLCV_Comparison_Data)['close'];



    $traceData = [
      'custom1'=>$H2_open,
      'custom2'=>$H2_close
    ];
    $this->setTrace('tick',$traceData);

    $shortCondition = MathUtils::crossunder($OHLCV_Comparison_Data,'close','open');
    $longCondition = MathUtils::crossover($OHLCV_Comparison_Data,'close','open');

    $latest = last($this->MARKET_data);

    if ($this->hasOpenPosition()) {
      //
      //
      //
      /////////////////////////////////////////////////
      //MANAGE POSITION


                  //Call in stop loss
                  //
                  if ($latest['close'] < $this->OpenPosition->stoploss) {
                    $this->closePosition(last($this->MARKET_data)['close']);
                    $traceData = [
                      'sell'=>last($this->MARKET_data)['close'],
                      'custom1'=>$H2_open,
                      'custom2'=>$H2_close
                    ];
                    $this->setTrace('stoploss',$traceData);
                  }



                  if (
                    //
                    // CONDITION FOR SHORT
                    //shortCondition = crossunder(security(tickerid, tim, close),security(tickerid, tim, open))
                    $shortCondition

                  ) {
                    //
                    //close position
                    $this->closePosition(last($this->MARKET_data)['close']);
                    $traceData = [
                      'sell'=>last($this->MARKET_data)['close'],
                      'custom1'=>$H2_open,
                      'custom2'=>$H2_close
                    ];
                    $this->setTrace('sell',$traceData);


                  }






      /////////////////////////////////////////////////
    } else {
      //
      //
      //
      /////////////////////////////////////////////////
      //HUNT FOR ENTRY
      //longCondition = n1>n2 and strategy.opentrades<ot and confidence>dt and close>n2 and leadLine1>leadLine2 and open<LS and MACD>aMACD
                  if (
                    //
                    //longCondition = crossover(security(tickerid, tim, close),security(tickerid, tim, open))
                    $longCondition
                    //
                  ) {
                    //
                    //
                    //OPEN LONG POSITION
                    $this->openPosition(last($this->MARKET_data)['close'], last($this->MARKET_data)['close']*0.975,last($this->MARKET_data)['close']*1.01);
                    $traceData = [
                      'buy'=>last($this->MARKET_data)['close'],
                      'custom1'=>$H2_open,
                      'custom2'=>$H2_close

                    ];
                    $this->setTrace('buy',$traceData);
                  }



      /////////////////////////////////////////////////
    }


  }















}

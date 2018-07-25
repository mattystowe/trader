<?php
/*
* RinseMachineV1 Strategy - 30m Trading Ichimoku cloud
*
*
*
* Relies on 1d comparison data for the same instrument for confidence scoring.
*
*
*
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

class RinseMachineV1 extends BaseStrategy
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


    $keh = 14; //Double HullMA length
    $dt = 0.0010; // Decision threshold (0.001)
    $SL = 500.00; // Stop Loss
    $TP = 25000.00; //Target Profit Point
    $ot = 1; //open trades


    $conversionPeriods = 9; //Conversion Line Periods
    $basePeriods = 26; //Base Line Periods
    $laggingSpan2Periods = 52; //Lagging Span 2 Periods
    $displacement = 26; //Displacement

    $MACD_Length = 9;
    $MACD_fastLength = 12;
    $MACD_slowLength = 26;


    //n2ma calc
    //n2ma=2*wma(close,round(keh/2))
    $wma = Trader::wma(array_column($this->MARKET_data,'close'),round($keh/2));
    foreach ($wma as $key => $value) {
      $wma[$key] = $value * 2;
    }
    $n2ma = $wma;
    //dump($n2ma);

    //nma calc
    //nma=wma(close,keh)
    $nma = Trader::wma(array_column($this->MARKET_data,'close'),$keh);

    //diff calc
    //diff=n2ma-nma
    $diff = [];
    foreach ($n2ma as $key => $value) {
      if (array_key_exists($key,$nma)) {
        $diff[] = $n2ma[$key] - $nma[$key];
      }
    }

    //sqn calc
    //sqn=round(sqrt(keh))
    $sqn=round(sqrt($keh));



    //n2ma1 calc
    //n2ma1=2*wma(close[1],round(keh/2))
    $data_copy = $this->MARKET_data;
    array_pop($data_copy); // take 1 off the end to get back to previous tick
    $close_data = array_column($data_copy,'close');
    $wma = Trader::wma($close_data,round($keh/2));
    foreach ($wma as $key => $value) {
      $wma[$key] = $value * 2;
    }
    $n2ma1 = $wma;


    //nma1 calc
    //nma1=wma(close[1],keh)
    $data_copy = $this->MARKET_data;
    array_pop($data_copy); // take 1 off the end to get back to previous tick
    $close_data = array_column($data_copy,'close');
    $nma1 = Trader::wma($close_data,$keh);

    //diff1 calc
    //diff1=n2ma1-nma1
    $diff1 = [];
    foreach ($n2ma1 as $key => $value) {
      if (array_key_exists($key,$nma1)) {
        $diff1[] = $n2ma1[$key] - $nma1[$key];
      }
    }

    //sqn1 calc
    //sqn1=round(sqrt(keh))
    $sqn1=round(sqrt($keh));


    //n1 calc
    //n1=wma(diff,sqn)
    $n1 = Trader::wma($diff, $sqn);

    //n2 calc
    //n2=wma(diff1,sqn)
    $n2 = Trader::wma($diff1,$sqn);


    //Confidence scoring based on 1d chart position
    //confidence=(security(tickerid, 'D', close)-security(tickerid, 'D', close[1]))/security(tickerid, 'D', close[1])
    $OHLCV_Comparison_Data = $this->loadComparisonTimeFrameOHLCV('1d');
    $Comparison_close = MathUtils::arrayLast($OHLCV_Comparison_Data)['close'];
    $Comparison_previous_close = MathUtils::arrayPrevious($OHLCV_Comparison_Data)['close'];
    $confidence = ($Comparison_close - $Comparison_previous_close) / $Comparison_previous_close;




    $conversionLine = $this->donchian($conversionPeriods);
    $baseLine = $this->donchian($basePeriods);
    $leadLine1 = ($conversionLine + $baseLine) / 2;
    $leadLine2 = $this->donchian($laggingSpan2Periods);


    $LS = MathUtils::arrayPosition(array_column($this->MARKET_data,'close'),$displacement);

    //MACD calc with alpha
    //MACD = ema(close, MACD_fastLength) - ema(close, MACD_slowLength)
    $macd_ema1 = Trader::ema(array_column($this->MARKET_data,'close'),$MACD_fastLength);
    $macd_ema2 = Trader::ema(array_column($this->MARKET_data,'close'),$MACD_slowLength);
    $MACD = [];
    foreach ($macd_ema1 as $key => $value) {
      if (array_key_exists($key,$macd_ema2)) {
        $MACD[] = $macd_ema1[$key] - $macd_ema2[$key];
      }
    }
    //aMACD calculation
    //aMACD = ema(MACD, MACD_Length)

    $aMACD = Trader::ema($MACD, $MACD_Length);


    $traceData = [
      'custom1'=>last($n2ma),
      'custom2'=>last($nma),
      'custom3'=>last($diff),
      'custom4'=>last($n2ma1),
      'custom5'=>last($nma1),
      'custom6'=>last($diff1),
      'custom7'=>last($n1),
      'custom8'=>last($n2),
      'custom9'=>$confidence,
      'custom10'=>$conversionLine,
      'custom11'=>$baseLine,
      'custom12'=>$leadLine1,
      'custom13'=>$leadLine2,
      'custom14'=>last($LS),
      'custom15'=>last($macd_ema1),
      'custom16'=>last($macd_ema2),
      'custom17'=>last($aMACD),

    ];
    $this->setTrace('log',$traceData);





    if ($this->hasOpenPosition()) {
      //
      //
      //
      /////////////////////////////////////////////////
      //MANAGE POSITION
      //closelong = n1<n2 and close<n2 and confidence<dt or strategy.openprofit<SL or strategy.openprofit>TP


      if (
        (last($n1) < last($n2))
        &&
        (last($this->MARKET_data)['close']<last($n2))
        &&
        ($confidence < $dt)
      ) {
        //
        //close position
        $this->closePosition(last($this->MARKET_data)['close']);
        $traceData = [
          'sell'=>last($this->MARKET_data)['close'],
          'custom1'=>last($n2ma),
          'custom2'=>last($nma),
          'custom3'=>last($diff),
          'custom4'=>last($n2ma1),
          'custom5'=>last($nma1),
          'custom6'=>last($diff1),
          'custom7'=>last($n1),
          'custom8'=>last($n2),
          'custom9'=>$confidence,
          'custom10'=>$conversionLine,
          'custom11'=>$baseLine,
          'custom12'=>$leadLine1,
          'custom13'=>$leadLine2,
          'custom14'=>last($LS),
          'custom15'=>last($macd_ema1),
          'custom16'=>last($macd_ema2),
          'custom17'=>last($aMACD),

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
        (last($n1) > last($n2))
        &&
        ($confidence > $dt)
        &&
        (last($this->MARKET_data)['close']>last($n2))
        &&
        ($leadLine1 > $leadLine2)
        &&
        (last($this->MARKET_data)['open'] < last($LS))
        &&
        (last($MACD) > last($aMACD))
      ) {
        //
        //
        //OPEN LONG POSITION
        $this->openPosition(last($this->MARKET_data)['close'], last($this->MARKET_data)['close']*0.95,last($this->MARKET_data)['close']*1.01);
        $traceData = [
          'buy'=>last($this->MARKET_data)['close'],
          'custom1'=>last($n2ma),
          'custom2'=>last($nma),
          'custom3'=>last($diff),
          'custom4'=>last($n2ma1),
          'custom5'=>last($nma1),
          'custom6'=>last($diff1),
          'custom7'=>last($n1),
          'custom8'=>last($n2),
          'custom9'=>$confidence,
          'custom10'=>$conversionLine,
          'custom11'=>$baseLine,
          'custom12'=>$leadLine1,
          'custom13'=>$leadLine2,
          'custom14'=>last($LS),
          'custom15'=>last($macd_ema1),
          'custom16'=>last($macd_ema2),
          'custom17'=>last($aMACD),

        ];
        $this->setTrace('buy',$traceData);
      }



      /////////////////////////////////////////////////
    }


  }





  //
  //
  //donchian(len) => avg(lowest(len), highest(len))
  //
  //
  public function donchian($len) {
    $lowest = Trader::min(array_column($this->MARKET_data,'low'),$len);
    $highest = Trader::max(array_column($this->MARKET_data,'high'),$len);
    $l = array_pop($lowest);
    $h = array_pop($highest);
    return ($l+$h)/2;
  }












}

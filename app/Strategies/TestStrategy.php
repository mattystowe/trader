<?php
/*
*Test Strategy Example
*
*
*
*
*
*
*
*/
namespace App\Strategies;

use Log;
use \App\MathUtils;
use \App\Activemarket;
use \App\Market;
use \App\Activestrategy;
use \App\Strategy;
use \App\Candles;
use \App\Tracer;
use \App\Signals;


class TestStrategy extends BaseStrategy
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

      //
      //setup anything here before being processed


  }






  /////////////////////////////////////
  //All strategies are called to think!
  //
  //
  //
  public function think() {





    $this->addIndicators([
      [
        'type'=>'bbands',
        'prefix'=>'bbands',
        'period'=>10,
        'StDevUp'=>2,
        'StDevDown'=>2,
        'MovingAverageType'=>'SMA',
        'Source'=>'close'
      ],
      [
        'type'=>'mfi',
        'prefix'=>'mfi',
        'period'=>14,
      ],
      [
        'type'=>'macd',
        'prefix'=>'macd',
        'fastLength'=>12,
        'slowLength'=>26,
        'smoothing'=>9,
        'Source'=>'close'
      ],
      [
        'type'=>'stoch',
        'prefix'=>'stoch',
        'Kperiod'=>5,
        'Ksmoothing'=>3,
        'KMovingAverageType'=>'SMA',
        'Dsmoothing'=>3,
        'DMovingAverageType' =>'SMA'
      ],
      [
        'type'=>'stochrsi',
        'prefix'=>'stochrsi',
        'period'=>14,
        'Kperiod'=>3,
        'Dperiod'=>3,
        'MovingAverageType'=>'SMA',
        'Source'=>'close'

      ],
      [
        'type'=>'willr',
        'prefix'=>'willr',
        'period'=>14
      ],
      [
        'type'=>'aroon',
        'prefix'=>'aroon',
        'period'=>14,
      ]
    ]);

    //$signals = new Signals;
    /*$bbands = $signals->bbands($this->MARKET_data);
    $willr = $signals->willr($this->MARKET_data);
    $macd = $signals->macd($this->MARKET_data);
    $mfi = $signals->macd($this->MARKET_data);
    $aroon = $signals->aroon($this->MARKET_data);*/
    //$this->setTrace('BUY',$signals->getScorecard($this->MARKET_data));
    //Log::debug('Score:' . $signals->getScorecard($this->MARKET_data));




    if ($this->hasOpenPosition()) {
      //
      //
      //Manage currently open position.
      //$this->managePosition();

    } else {
      //
      //
      //Look for new entry point.
      $this->huntForEntry();
    }

    $this->setTrace('TICK');


  }







  public function huntForEntry() {

    $current = MathUtils::arrayLast($this->MARKET_data);
    $signals = new Signals;
    $bbands = $signals->bbands($this->MARKET_data);
    //$aroon = $signals->aroon($this->MARKET_data);
    if ($bbands == 1) {
      //$this->setTrace('BUY',$current['close']);
    }

    /*$current = MathUtils::arrayLast($this->MARKET_data);
    $prior = MathUtils::arrayPrevious($this->MARKET_data);


    if (MathUtils::consecutiveHighs($this->MARKET_data,'high',3)) {
      if (MathUtils::consecutiveHighs($this->MARKET_data,'low',2)) {
        if ($current['willrR']>=-60) {
          //if ($current['mfiMFI']<=30) {
            if (MathUtils::containsValueBelow(-80,$this->MARKET_data,'willrR',5)) {
              $this->setTrace('BUY',$current['close']);
            }
          //}
        }
      }
    }*/



    /*if ($current['low']<=$current['bbandsLowerBand']) {
      if ($current['stochK']<=24 && $current['stochD']<=24) {
          //if ($current['stochK']>=$current['stochD']) {
          if ($current['mfiMFI']<=30) {
            $this->setTrace('BUY',$current['close']);
            $this->openPosition($current['close'], $current['close']*0.95,$current['close']*1.01); // set the profit target to 1% and initial stoploss to -0.5%
          }
          //}
      }
    }*/

    /*if ($current['mfiMFI']<=25) {
      if ($current['macdMACDHist']>=0) {
        $this->setTrace('BUY',$current['close']);
        $this->openPosition($current['close'], $current['low'],$current['close']*1.01); // set the profit target to 1% and initial stoploss to current close.

      }
    }*/

  }








  public function managePosition() {

    $current = MathUtils::arrayLast($this->MARKET_data);
    $prior = MathUtils::arrayPrevious($this->MARKET_data);


    //check if stop loss needs to be called in.
    if ($current['close'] < $this->OpenPosition->stoploss) {
      $this->setTrace('SELL',$current['close']);
      $this->closePosition($current['close']);
      Log::debug($this->_market->symbol . ' Stop loss called in at ' . $this->OpenPosition->stoploss);
      return true;
    }


    //check if profit target reached - if so, trail stoploss and increase profit target a further 1%
    if ($current['close'] > $this->OpenPosition->stoplimit) {
      $this->OpenPosition->stoploss = $current['low'];
      $this->OpenPosition->stoplimit = $this->OpenPosition->stoplimit*1.01;
      $this->OpenPosition->save();
      Log::debug($this->_market->symbol . ' Profit Target reached. Trail Stop Loss to ' . $this->OpenPosition->stoploss);
      return true;
    }



    if ($current['close']>=$current['bbandsMiddleBand']) {
      //if ($current['stochK']>=80 && $current['stochD']>=80) {
          //if ($current['stochK']<=$current['stochD']) {
            $this->OpenPosition->stoploss = $current['close'];
            $this->OpenPosition->save();
            Log::debug($this->_market->symbol . ' BBMiddle reached. Trail Stop Loss to ' . $this->OpenPosition->stoploss);
            return true;
          //}
        //}

    }


      if ($current['close']>=$current['bbandsUpperBand']) {
        if ($current['stochK']>=80 && $current['stochD']>=80) {
            //if ($current['stochK']<=$current['stochD']) {
              $this->OpenPosition->stoploss = $current['close'];
              $this->OpenPosition->save();
              Log::debug($this->_market->symbol . ' BBUpper reached. Trail Stop Loss to ' . $this->OpenPosition->stoploss);
              return true;
            //}
          }

      }





    /*if ($current['low']>$this->OpenPosition->stoploss) {
      $this->OpenPosition->stoploss = $current['low'];
      $this->OpenPosition->save();
      Log::debug($this->_market->symbol . ' Trail Stop Loss to ' . $this->OpenPosition->stoploss);
      return true;
    }*/





  }








  public function setTrace($message,$value = 0) {
    $tracer = new Tracer;
    $tracer->market_id = $this->_market->id;
    $tracer->strategy_id = $this->_strategy->id;
    $tracer->utctimestamp = MathUtils::arrayLast($this->MARKET_data)['utctimestamp'];
    $tracer->timestamp_datetime = MathUtils::arrayLast($this->MARKET_data)['timestamp_datetime'];
    $tracer->message = $message;
    $tracer->open = MathUtils::arrayLast($this->MARKET_data)['open'];
    $tracer->high = MathUtils::arrayLast($this->MARKET_data)['high'];
    $tracer->low = MathUtils::arrayLast($this->MARKET_data)['low'];
    $tracer->close = MathUtils::arrayLast($this->MARKET_data)['close'];
    $tracer->volume = MathUtils::arrayLast($this->MARKET_data)['volume'];

    $tracer->custom1 = MathUtils::arrayLast($this->MARKET_data)['bbandsLowerBand'];
    $tracer->custom2 = MathUtils::arrayLast($this->MARKET_data)['bbandsMiddleBand'];
    $tracer->custom3 = MathUtils::arrayLast($this->MARKET_data)['bbandsUpperBand'];

    $tracer->custom4 = MathUtils::arrayLast($this->MARKET_data)['mfiMFI'];

    $tracer->custom6 = MathUtils::arrayLast($this->MARKET_data)['macdMACDMACD'];
    $tracer->custom7 = MathUtils::arrayLast($this->MARKET_data)['macdMACDSignal'];
    $tracer->custom8 = MathUtils::arrayLast($this->MARKET_data)['macdMACDHist'];

    $tracer->custom11 = MathUtils::arrayLast($this->MARKET_data)['stochK'];
    $tracer->custom12 = MathUtils::arrayLast($this->MARKET_data)['stochD'];

    //$tracer->custom13 = MathUtils::arrayLast($this->MARKET_data)['stochrsiFastK'];
    //$tracer->custom14 = MathUtils::arrayLast($this->MARKET_data)['stochrsiFastD'];

    $tracer->custom13 = MathUtils::arrayLast($this->MARKET_data)['aroonUp'];
    $tracer->custom14 = MathUtils::arrayLast($this->MARKET_data)['aroonDown'];

    $tracer->custom15 = MathUtils::arrayLast($this->MARKET_data)['willrR'];

    switch ($message) {
      case 'BUY':
        $tracer->custom9 = $value;
        break;
        case 'SELL':
        $tracer->custom10 = $value;
        break;
    }
    $tracer->save();
  }





}

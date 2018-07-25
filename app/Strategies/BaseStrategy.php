<?php

namespace App\Strategies;


use Log;
use \App\Activemarket;
use \App\Market;
use \App\Activestrategy;
use \App\Strategy;
use \App\Ohlcv;
use \App\Indicators;
use \App\Candles;
use \App\Position;
use Carbon\Carbon;
use \App\Signals;
use \App\MathUtils;
use \App\Tracer;



class BaseStrategy
{

  public $_activestrategy;
  public $_strategy;
  public $_activemarket;
  public $_market;
  public $_account;
  public $_exchange;


  public $MARKET_data = array(); // master market data

  public $OpenPosition; // holds the open position if present.

  public $backtest = false;
  public $backtest_skip = 0;
  public $backtest_pick = 0; // walk cycle for back testing data.


  function __construct(Activestrategy $activestrategy, Activemarket $activemarket, $backtest = false,$backtest_skip = 0, $backtest_pick = 0)
    {
      $this->backtest = $backtest;
      $this->backtest_skip = $backtest_skip;
      $this->backtest_pick = $backtest_pick;

      $this->_activestrategy = $activestrategy;
      $this->_strategy = $activestrategy->strategy;
      $this->_activemarket = $activemarket;
      $this->_market = $activemarket->market;
      $this->_account = $activestrategy->account;
      $this->_exchange = $activestrategy->account->exchange;

      if ($this->backtest) {
        $this->backtestOHLCV();
      } else {
        $this->loadOHLCV();
      }
      Log::debug($this->_market->symbol . ' c:' . last($this->MARKET_data)['close']. ' h:' . last($this->MARKET_data)['high']. ' l:' . last($this->MARKET_data)['low']);
    }


    public function think() {
      //placeholder for thinking.
    }








    public function loadOHLCV() {
      $ohlcv_data = Ohlcv::where([
                          ['market_id','=',$this->_market->id],
                          ['timeframe','=',$this->_strategy->timeframe]
                          ])
                          ->orderBy('utctimestamp','desc')
                          ->limit(250)
                          ->get();
      //reverse the collection to show the lates
      $reversed = $ohlcv_data->reverse();

      //prepare the master data.
      $this->MARKET_data = array();
      foreach ($reversed as $data) {
        $this->MARKET_data[] = [
            'utctimestamp'=>$data->utctimestamp,
            'timestamp_datetime'=>$data->timestamp_datetime,
            'open'=>$data->open,
            'high'=>$data->high,
            'low'=>$data->low,
            'close'=>$data->close,
            'volume'=>$data->volume
        ];
      }

    }



    public function backtestOHLCV() {
      $ohlcv_data = Ohlcv::where([
                          ['market_id','=',$this->_market->id],
                          ['timeframe','=',$this->_strategy->timeframe]
                          ])
                          ->orderBy('utctimestamp','asc')
                          ->skip($this->backtest_skip)
                          ->take($this->backtest_pick)
                          ->get();

      $this->MARKET_data = array();
      foreach ($ohlcv_data as $data) {
        $this->MARKET_data[] = [
            'utctimestamp'=>$data->utctimestamp,
            'timestamp_datetime'=>$data->timestamp_datetime,
            'open'=>$data->open,
            'high'=>$data->high,
            'low'=>$data->low,
            'close'=>$data->close,
            'volume'=>$data->volume
        ];
      }

    }






    public function loadComparisonTimeFrameOHLCV($timeframe) {
      if ($this->backtest) {
        return $this->backtestComparisonOHLCV($timeframe);
      } else {
        return $this->comparisonOHLCV($timeframe);
      }

    }




    private function backtestComparisonOHLCV($timeframe) {
      $current = MathUtils::arrayLast($this->MARKET_data);

      $ohlcv_data = Ohlcv::where([
                          ['market_id','=',$this->_market->id],
                          ['timeframe','=',$timeframe],
                          ['timestamp_datetime','<=',$current['timestamp_datetime']]
                          ])
                          ->orderBy('utctimestamp','desc')
                          ->limit(250)
                          ->get();
                          //reverse the collection to show the lates
                          $reversed = $ohlcv_data->reverse();

                          //prepare the master data.
                          $return_data = array();
                          foreach ($reversed as $data) {
                            $return_data[] = [
                                'utctimestamp'=>$data->utctimestamp,
                                'timestamp_datetime'=>$data->timestamp_datetime,
                                'open'=>$data->open,
                                'high'=>$data->high,
                                'low'=>$data->low,
                                'close'=>$data->close,
                                'volume'=>$data->volume
                            ];
                          }

                          return $return_data;
    }



    private function comparisonOHLCV($timeframe) {
      $ohlcv_data = Ohlcv::where([
                          ['market_id','=',$this->_market->id],
                          ['timeframe','=',$timeframe]
                          ])
                          ->orderBy('utctimestamp','desc')
                          ->limit(250)
                          ->get();
      //reverse the collection to show the lates
      $reversed = $ohlcv_data->reverse();

      //prepare the master data.
      $return_data = array();
      foreach ($reversed as $data) {
        $return_data[] = [
            'utctimestamp'=>$data->utctimestamp,
            'timestamp_datetime'=>$data->timestamp_datetime,
            'open'=>$data->open,
            'high'=>$data->high,
            'low'=>$data->low,
            'close'=>$data->close,
            'volume'=>$data->volume
        ];
      }

      return $return_data;
    }









    //Add indicators to the market data
    //
    //
    //
    public function addIndicators($indicator_specs) {
      foreach ($indicator_specs as $indicator_spec) {
        $indicators = new Indicators;
        $method = $indicator_spec['type'];
        $result = $indicators->{$method}($this->MARKET_data, $indicator_spec);
        $this->MARKET_data = $result;
      }
    }





    public function openPosition($entry_price, $stoploss = null, $stoplimit = null) {
      if ($this->_activemarket->active) {

          $position = new Position;
          $position->activestrategy_id = $this->_activestrategy->id;
          $position->activemarket_id = $this->_activemarket->id;
          $position->entry_price = $entry_price;
          if ($stoploss != null) { $position->stoploss = $stoploss; }
          if ($stoplimit != null) { $position->stoplimit = $stoplimit; }
          $position->status = 'OPEN';
          if ($this->backtest) {
            //add current time
            //add current time
            //
            $position->entry_time = last($this->MARKET_data)['timestamp_datetime'];
          } else {
            //add exchange time
            $position->entry_time = Carbon::now()->toDateTimeString();
          }

          if ($position->save()) {
            //
            Log::debug($this->_market->symbol . ' ' . $entry_price . ' Position Opened ');
            //
            //
            //Broadcast Event
            event(new \App\Events\PositionOpened($position));
            //
          } else {
            Log::error($this->_market->symbol . ' ' . $entry_price . ' Could not open position.');
          }
      } else {
        Log::debug($this->_market->symbol . ' Not Active - aborting position opening.');
      }
    }


    public function closePosition($exit_price) {
      $this->OpenPosition->status = 'CLOSED';
      $this->OpenPosition->exit_price = $exit_price;
      if ($this->backtest) {
        //add current time
        $this->OpenPosition->exit_time = last($this->MARKET_data)['timestamp_datetime'];
      } else {
        //add exchange time
        $this->OpenPosition->exit_time = Carbon::now()->toDateTimeString();
      }
      if ($this->OpenPosition->save()) {
        Log::debug($this->_market->symbol . ' ' . $exit_price . ' Position Closed ');
        //
        //
        //Broadcast Event
        event(new \App\Events\PositionClosed($this->OpenPosition));
        //
      } else {
        Log::error($this->_market->symbol . ' ' . $exit_price . ' Could not close position.');
      }
    }




    /*
    * Checks if this has open position.
    * Returns Bool
    * Sets the $this->OpenPosition to the Position.
    *
    */
    public function hasOpenPosition() {
      $position = Position::where([
        ['activestrategy_id','=',$this->_activestrategy->id],
        ['activemarket_id','=',$this->_activemarket->id],
        ['status','=','OPEN']
      ])->get();
      //dump($this->OpenPosition);
      if ($position->isNotEmpty()) {
        $this->OpenPosition = $position[0];
        return true;
      } else {
        return false;
      }
    }







    public function setTrace($message, $data = []) {
      $trace = env('TRACE');
      if ($trace == 'TRUE') {
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


          //fill in data
          foreach ($data as $field => $value) {
            $tracer->{$field} = $value;
          }

          $tracer->save();
      }
    }






}

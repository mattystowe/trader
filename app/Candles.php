<?php

namespace App;

use Log;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\Trader;

class Candles
{
  /**
     * @var array
     *
     *      Here is all the candles included in the trader library
     */
    public $candles = array (
        'cdl2crows'              => 'Two Crows',
        'cdl3blackcrows'         => 'Three Black Crows',
        'cdl3inside'             => 'Three Inside Up/Down',
        'cdl3linestrike'         => 'Three-Line Strike',
        'cdl3outside'            => 'Three Outside Up/Down',
        'cdl3starsinsouth'       => 'Three Stars In The South',
        'cdl3whitesoldiers'      => 'Three Advancing White Soldiers',
        'cdlabandonedbaby'       => 'Abandoned Baby',
        'cdladvanceblock'        => 'Advance Block',
        'cdlbelthold'            => 'Belt-hold',
        'cdlbreakaway'           => 'Breakaway',
        'cdlclosingmarubozu'     => 'Closing Marubozu',
        'cdlconcealbabyswall'    => 'Concealing Baby Swallow',
        'cdlcounterattack'       => 'Counterattack',
        'cdldarkcloudcover'      => 'Dark Cloud Cover',
        'cdldoji'                => 'Doji',
        'cdldojistar'            => 'Doji Star',
        'cdldragonflydoji'       => 'Dragonfly Doji',
        'cdlengulfing'           => 'Engulfing Pattern',
        'cdleveningdojistar'     => 'Evening Doji Star',
        'cdleveningstar'         => 'Evening Star',
        'cdlgapsidesidewhite'    => 'Up/Down-gap side-by-side white lines',
        'cdlgravestonedoji'      => 'Gravestone Doji',
        'cdlhammer'              => 'Hammer',
        'cdlhangingman'          => 'Hanging Man',
        'cdlharami'              => 'Harami Pattern',
        'cdlharamicross'         => 'Harami Cross Pattern',
        'cdlhighwave'            => 'High-Wave Candle',
        'cdlhikkake'             => 'Hikkake Pattern',
        'cdlhikkakemod'          => 'Modified Hikkake Pattern',
        'cdlhomingpigeon'        => 'Homing Pigeon',
        'cdlidentical3crows'     => 'Identical Three Crows',
        'cdlinneck'              => 'In-Neck Pattern',
        'cdlinvertedhammer'      => 'Inverted Hammer',
        'cdlkicking'             => 'Kicking',
        'cdlkickingbylength'     => 'Kicking - bull/bear determined by the longer marubozu',
        'cdlladderbottom'        => 'Ladder Bottom',
        'cdllongleggeddoji'      => 'Long Legged Doji',
        'cdllongline'            => 'Long Line Candle',
        'cdlmarubozu'            => 'Marubozu',
        'cdlmatchinglow'         => 'Matching Low',
        'cdlmathold'             => 'Mat Hold',
        'cdlmorningdojistar'     => 'Morning Doji Star',
        'cdlmorningstar'         => 'Morning Star',
        'cdlonneck'              => 'On-Neck Pattern',
        'cdlpiercing'            => 'Piercing Pattern',
        'cdlrickshawman'         => 'Rickshaw Man',
        'cdlrisefall3methods'    => 'Rising/Falling Three Methods',
        'cdlseparatinglines'     => 'Separating Lines',
        'cdlshootingstar'        => 'Shooting Star',
        'cdlshortline'           => 'Short Line Candle',
        'cdlspinningtop'         => 'Spinning Top',
        'cdlstalledpattern'      => 'Stalled Pattern',
        'cdlsticksandwich'       => 'Stick Sandwich',
        'cdltakuri'              => 'Takuri (Dragonfly Doji with very long lower shadow)',
        'cdltasukigap'           => 'Tasuki Gap',
        'cdlthrusting'           => 'Thrusting Pattern',
        'cdltristar'             => 'Tristar Pattern',
        'cdlunique3river'        => 'Unique 3 River',
        'cdlupsidegap2crows'     => 'Upside Gap Two Crows',
        'cdlxsidegap3methods'    => 'Upside/Downside Gap Three Methods'
    );




    /**
         * @var array
         *  candles which have been identified to act
         *  counter to their purpose, the percentage value is the percentage of
         *  the counter purpose flip:
         *  i.e 3linestrike 84% of the time is a bullish reversal
         */
        protected $counter_purpose = [
            'bear' => [
                 '2crows'           => 60
                ,'3linestrike'      => 84
                ,'advanceblock'     => 64
                ,'dojistar'         => 69
                ,'hangingman'       => 59
                ,'stalledpattern'   => 77
                ,'tasukigap'        => 54
                ,'thrusting'        => 57
                ,'upsidegap2crows'  => 60
                ,'xsidegap3methods' => 62
            ]
            ,'bull' => [
                 '3linestrike'      => 63
                ,'concealbabyswall' => 75
                ,'dojistar'         => 64
                ,'homingpigeon'     => 56
                ,'matchinglow'      => 61
                ,'sticksandwich'    => 62
                ,'unique3river'     => 60
                ,'xsidegap3methods' => 59
            ]
        ];

        /**
         * @var array
         * bearish and bullish price reversal patterns with
         * percentages of actual reversals.
         */
        protected $price_reversal = [
            'bear' => [
                 '2crows'          => 60
                ,'3blackcrows'     => 78
                ,'3inside'         => 60
                ,'3outside'        => 69
                ,'abandonedbaby'   => 69
                ,'advanceblock'    => 64
                ,'belthold'        => 68
                ,'breakaway'       => 63
                ,'counterattack'   => 60
                ,'darkcloudcover'  => 60
                ,'doji'            => 52
                ,'engulfing'       => 79
                ,'eveningdojistar' => 71
                ,'eveningstar'     => 72
                ,'harami'          => 53
                ,'hikkake'         => 50
                ,'identical3crows' => 78
                ,'kicking'         => 54
                ,'shootingstar'    => 59
                ,'stalledpattern'  => 77
                ,'tristar'         => 52
            ]
            ,'bull' => [
                 '3inside'         => 65
                ,'3outside'        => 75
                ,'3starsinsouth'   => 86
                ,'3whitesoldiers'  => 82
                ,'abandonedbaby'   => 70
                ,'belthold'        => 71
                ,'breakaway'       => 59
                ,'counterattack'   => 66
                ,'doji'            => 51
                ,'engulfing'       => 63
                ,'hammer'          => 60
                ,'harami'          => 53
                ,'hikkake'         => 52
                ,'homingpigeon'    => 56
                ,'invertedhammer'  => 65
                ,'kicking'         => 53
                ,'ladderbottom'    => 56
                ,'morningdojistar' => 76
                ,'morningstar'     => 78
                ,'piercing'        => 64
                ,'sticksandwich'   => 62
                ,'takuri'          => 66
                ,'tristar'         => 60
                ,'unique3river'    => 60
            ]
        ];

        /**
         * @var array
         * These candles indicate indecision
         * if these candles are present, then avoid entering positions.
         */
        protected $indecision = [
             'gravestonedoji'
            ,'longleggeddoji'
            ,'highwave'
            ,'rickshawman'
            ,'shortline'
            ,'spinningtop'
        ];

        /**
         * @var array
         * price continuation candle patterns with percentages
         */
        protected $price_continuation = [
            'bear' => [
                 'closingmarubozu'  => 52
                ,'hikkake'          => 50
                ,'inneck'           => 53
                ,'longline'         => 53
                ,'marubozu'         => 53
                ,'onneck'           => 56
                ,'risefall3methods' => 71
                ,'separatinglines'  => 63
            ]
            ,'bull' => [
                 'closingmarubozu'  => 55
                ,'gapsidesidewhite' => 66
                ,'hikkake'          => 50
                ,'longline'         => 58
                ,'marubozu'         => 56
                ,'mathold'          => 78
                ,'risefall3methods' => 74
                ,'separatinglines'  => 72
            ]
        ];









  /*
  *          Run our dataset against ALL the trader candles types
     *          return an array matching the datapoints:
     *
     *          if array entry is non-zero, > 0 bullish, < 0 bearish
     *
     *          - notfound  = candle not found anywhere in entire dataset
     *          - range     = candle found in the entire dataset
     *          - recently  = candle found in the most recent three periods (days/hours)
     *          - current   = candle found in the single most recent
     *          - datafor   = close data surrounding the candle on either side
  */
  public function getAll($data, $period = 100) {
    $ret = array();


    //TODO - limit data to $period - max number of periods to consider



    $open = array_column($data, 'open');
    $high = array_column($data, 'high');
    $low = array_column($data, 'low');
    $close = array_column($data, 'close');





        foreach($this->candles as $cdlfunc => $name) {

                $tempdata = Trader::$cdlfunc($open, $high, $low, $close);
                if (empty($tempdata)) {
                    continue;
                }

                $cdlfunc = str_replace('trader_cdl','', $cdlfunc);

                $tmp = array_map('abs', $tempdata); // remove negatives
                $sum = array_sum($tmp);             // sum it all
                if ($sum == 0) {
                    $ret['notfound'][$cdlfunc] = $name;
                }
                foreach ($tempdata as $key => $temp) {
                    $ret['all'][$cdlfunc] = $temp;
                    if (abs($temp) > 0) {
                        $ret['range'][$cdlfunc]    = $name; // that we found this candle
                        $ret['location'][$cdlfunc][] = $key;  // the location in the dataset where this candle is
                    }
                }

                $tempdataReIndexed = array_values($tempdata);
                $closeData = array_values($close);
                foreach ($tempdataReIndexed as $idx => $cand) {
                    $sindex = (($idx)-3 < 0 ? 3 : $idx);
                    if ($sindex+4 > count($closeData)){
                        $sindex = $sindex - 4;
                    }
                    $currents = array_slice($closeData, $sindex-3, 7);
                    if ($cand <> 0) {
                        $lastfive = @implode(",", $currents);
                        $ret['datafor'][$cdlfunc] = $lastfive;
                    }
                }

                $lastBit = array_slice($tempdata, -3, 3);
                foreach ($lastBit as $test) {
                    if ($test <> 0) {
                        $ret['recently'][$cdlfunc] = $test;
                    }
                }
                $last = array_pop($tempdata);
                if ($last <> 0) {
                    $ret['current'][$cdlfunc] = $last;
                }

        }


        return $ret;
  }







  /**
     * @param $data
     *
     * @return mixed
     *
     *  returns array with weights
     * Array (
     *      [current] => Array (
     *              [indecision] => 100
     *              [reverse_bear] => 52
     *              [reverse_bear_total] => 52
     *              [reverse_bull] => 51
     *              [reverse_bull_total] => 51
     *      )
     *      [recent] => Array (
     *              [indecision] => 100
     *              [reverse_bear] => 68
     *              [reverse_bear_total] => 170
     *              [reverse_bull] => 71
     *              [reverse_bull_total] => 239
     *              [continue_bull] => 58
     *              [continue_bull_total] => 219
     *              [continue_bear] => 53
     *              [continue_bear_total] => 208
     *       )
     * )
     * Which would indicate we were coming from a place that should have reversed a bull run
     * and are not in a period of indecision.
     * in this case, we would not enter a trade.
     *
     */
  public function getCandleAnalysis($data, $period = 100) {

    //TODO - limit data to $period - max number of periods to consider



    $open = array_column($data, 'open');
    $high = array_column($data, 'high');
    $low = array_column($data, 'low');
    $close = array_column($data, 'close');


      $candle_data = $this->getAll($data);
      //dump($candle_data['current']);
      //dump($candle_data['recently']);

      $ret['indecision'] = 0;

      $price_reversal_bear_keys     = array_keys($this->price_reversal['bear']);
      $price_reversal_bull_keys     = array_keys($this->price_reversal['bull']);
      $price_continuation_bear_keys = array_keys($this->price_continuation['bear']);
      $price_continuation_bull_keys = array_keys($this->price_continuation['bull']);
      $counter_purpose_bear_keys    = array_keys($this->counter_purpose['bear']);
      $counter_purpose_bull_keys    = array_keys($this->counter_purpose['bull']);

      foreach($candle_data['current'] as $key => $data) {
          /** current indecision is bad */
          if (in_array($key, $this->indecision)) {
              $ret['indecision'] = $ret['indecision'] + 100;
          }

          /** price reversal */
          if (in_array($key, $price_reversal_bear_keys)){
              $ret['reverse_bear'] = $ret['reverse_bear'] ?? 0;
              $ret['reverse_bear'] = ($this->price_reversal['bear'][$key] > $ret['reverse_bear'] ? $this->price_reversal['bear'][$key] : $ret['reverse_bear']);
              $ret['reverse_bear_total'] =  (@$ret['reverse_bear_total'] + $this->price_reversal['bear'][$key] ?? $this->price_reversal['bear'][$key]);
          }
          if (in_array($key, $price_reversal_bull_keys)){
              $ret['reverse_bull'] = $ret['reverse_bull'] ?? 0;
              $ret['reverse_bull'] = ($this->price_reversal['bull'][$key] > $ret['reverse_bull'] ? $this->price_reversal['bull'][$key] : $ret['reverse_bull']);
              $ret['reverse_bull_total'] =  (@$ret['reverse_bull_total'] + $this->price_reversal['bull'][$key] ?? $this->price_reversal['bull'][$key]);
          }

          /** price continuation */
          if (in_array($key, $price_continuation_bull_keys) || in_array($key, $counter_purpose_bear_keys)){
              $ret['continue_bull'] = $ret['continue_bull'] ?? 0;
              $ret['continue_bull'] = ($this->price_continuation['bull'][$key] > $ret['continue_bull'] ? $this->price_continuation['bull'][$key] : $ret['continue_bull']);
              $ret['continue_bull_total'] =  (@$ret['continue_bull_total'] + $this->price_continuation['bull'][$key] ?? $this->price_continuation['bull'][$key]);
          }
          if (in_array($key, $price_continuation_bear_keys) || in_array($key, $counter_purpose_bull_keys)){
              $ret['continue_bear'] = $ret['continue_bear'] ?? 0;
              $ret['continue_bear'] = ($this->price_continuation['bear'][$key] > $ret['continue_bear'] ? $this->price_continuation['bear'][$key] : $ret['continue_bear']);
              $ret['continue_bear_total'] =  (@$ret['continue_bear_total'] + $this->price_continuation['bear'][$key] ?? $this->price_continuation['bear'][$key]);
          }
      }
      $return['current'] = $ret;
      $ret = [];
      $ret['indecision'] = 0;
      foreach($candle_data['recently'] as $key => $data) {
          /** recent indecision is okay, if we are done with it */
          if (in_array($key, $this->indecision)) {
              $ret['indecision'] = $ret['indecision'] + 100;
          }
          /** price reversal */
          if (in_array($key, $price_reversal_bear_keys)){
              $ret['reverse_bear'] = $ret['reverse_bear'] ?? 0;
              $ret['reverse_bear'] = ($this->price_reversal['bear'][$key] > $ret['reverse_bear'] ? $this->price_reversal['bear'][$key] : $ret['reverse_bear']);
              $ret['reverse_bear_total'] =  (@$ret['reverse_bear_total'] + $this->price_reversal['bear'][$key] ?? $this->price_reversal['bear'][$key]);
          }
          if (in_array($key, $price_reversal_bull_keys)){
              $ret['reverse_bull'] = $ret['reverse_bull'] ?? 0;
              $ret['reverse_bull'] = ($this->price_reversal['bull'][$key] > $ret['reverse_bull'] ? $this->price_reversal['bull'][$key] : $ret['reverse_bull']);
              $ret['reverse_bull_total'] =  (@$ret['reverse_bull_total'] + $this->price_reversal['bull'][$key] ?? $this->price_reversal['bull'][$key]);
          }

          /** price continuation */
          if (in_array($key, $price_continuation_bull_keys) || in_array($key, $counter_purpose_bear_keys)){
              $ret['continue_bull'] = $ret['continue_bull'] ?? 0;
              $ret['continue_bull'] = ($this->price_continuation['bull'][$key] > $ret['continue_bull'] ? $this->price_continuation['bull'][$key] : $ret['continue_bull']);
              $ret['continue_bull_total'] =  (@$ret['continue_bull_total'] + $this->price_continuation['bull'][$key] ?? $this->price_continuation['bull'][$key]);
          }
          if (in_array($key, $price_continuation_bear_keys) || in_array($key, $counter_purpose_bull_keys)){
              $ret['continue_bear'] = $ret['continue_bear'] ?? 0;
              $ret['continue_bear'] = ($this->price_continuation['bear'][$key] > $ret['continue_bear'] ? $this->price_continuation['bear'][$key] : $ret['continue_bear']);
              $ret['continue_bear_total'] =  (@$ret['continue_bear_total'] + $this->price_continuation['bear'][$key] ?? $this->price_continuation['bear'][$key]);
          }
      }
      $return['recent'] = $ret;
      return $return;



  }



}

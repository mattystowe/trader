<?php
namespace App;

use Log;
use App\MathUtils;
use App\Indicators;




class Signals
{


  public $available_signals = [
    'bbands' => 'Bollinger Bands',
    'willr' => 'Williams %R',
    'macd' => 'Moving Average Convergence/Divergence',
    'mfi' => 'Money Flow Index',
    'aroon' => 'Aroon Indicator'
  ];





  public function getScorecard($market_data) {
    //get signals
    $results = [];
    foreach ($this->available_signals as $method => $name) {
      $results[] = $this->{$method}($market_data);
    }
    return array_sum($results);
  }



  /**
   * @param string $pair
   * @param null   $data
   * @param int    $period
   *
   * @return int
   *
   * This algorithm uses the talib Bollinger Bands function to determine entry entry
   * points for long and sell/short positions.
   *
   * When the price breaks out of the upper Bollinger band, a sell or short position
   * is opened. A long position is opened when the price dips below the lower band.
   *
   *
   * Used to measure the market’s volatility.
   * They act like mini support and resistance levels.
   * Bollinger Bounce
   *
   * A strategy that relies on the notion that price tends to always return to the middle of the Bollinger bands.
   * You buy when the price hits the lower Bollinger band.
   * You sell when the price hits the upper Bollinger band.
   * Best used in ranging markets.
   * Bollinger Squeeze
   *
   * A strategy that is used to catch breakouts early.
   * When the Bollinger bands “squeeze”, it means that the market is very quiet, and a breakout is eminent.
   * Once a breakout occurs, we enter a trade on whatever side the price makes its breakout.
   */
    public function bbands($market_data) {

      $current_market = MathUtils::arrayLast($market_data);
      $previous_market = MathUtils::arrayPrevious($market_data);
      $indicators = new Indicators;
      $spec = [
        'type'=>'bbands',
        'prefix'=>'bbands',
        'period'=>10,
        'StDevUp'=>2,
        'StDevDown'=>2,
        'MovingAverageType'=>'SMA',
        'Source'=>'low'
      ];
      $bbands = $indicators->bbands($market_data, $spec, true);



      $lower = MathUtils::arrayLast($bbands['LowerBand']);
      $mid = MathUtils::arrayLast($bbands['MiddleBand']);
      $upper = MathUtils::arrayLast($bbands['UpperBand']);


      if ($current_market['close']<=$lower) {
        //
        //
        //
        if ($previous_market['high']<=$current_market['high']) {
          return 1;
        } else {
          return 0;
        }
      }

      if ($current_market['close']>=$upper) {
        return -1;
      }

      return 0;


    }




    /**
     *  Williams R%
     *  %R = (Highest High – Closing Price) / (Highest High – Lowest Low) x -100
     *  When the indicator produces readings from 0 to -20, this indicates overbought market conditions.
     *  When readings are -80 to -100, it indicates oversold market conditions.
     */
    public function willr($market_data) {
      $current_market = MathUtils::arrayLast($market_data);
      $indicators = new Indicators;
      $spec = [
        'type'=>'willr',
        'prefix'=>'willr',
        'period'=>14
      ];
      $willr = $indicators->willr($market_data, $spec, true);
      $current = MathUtils::arrayLast($willr);
      if ($current <= -80) {
            return 1; // oversold
        } elseif ($current >= -20) {
            return -1; // overbought
        } else {
            return 0;
        }

    }





    public function macd($market_data) {
      $current_market = MathUtils::arrayLast($market_data);
      $indicators = new Indicators;
      $spec = [
        'type'=>'macd',
        'prefix'=>'macd',
        'fastLength'=>12,
        'slowLength'=>26,
        'smoothing'=>9,
        'Source'=>'close'
      ];
      $macd = $indicators->macd($market_data, $spec, true);

        $macd_raw = $macd['macd'];
        $signal   = $macd['signal'];
        $hist     = $macd['hist'];

      //
      //TODO - detect crossover point in the last 2 periods.
      //
      //
      //
      //
      //
      $result = (MathUtils::arrayLast($macd_raw) - MathUtils::arrayLast($signal));
      # Close position for the pair when the MACD signal is negative
      if ($result < 0) {
          return -1;
      # Enter the position for the pair when the MACD signal is positive
      } elseif ($macd > 0) {
          return 1;
      } else {
          return 0;
      }


    }






    public function mfi($market_data) {
      $indicators = new Indicators;
      $spec = [
        'type'=>'mfi',
        'prefix'=>'mfi',
        'period'=>14,
      ];

      $mfi = $indicators->mfi($market_data, $spec, true);
      $currentmfi = MathUtils::arrayLast($mfi);

      if ($mfi > 80) {
            return -1; // overbought
        } elseif ($mfi < 10) {
            return 1;  // underbought
        } else {
            return 0;
        }
    }



    public function aroon($market_data) {
      $indicators = new Indicators;
      $spec = [
        'type'=>'aroon',
        'prefix'=>'aroon',
        'period'=>14,
      ];
      $aroon = $indicators->aroon($market_data, $spec, true);

      $Up = MathUtils::arrayLast($aroon['AroonUp']);
      $Down = MathUtils::arrayLast($aroon['AroonDown']);

      if ($Up >= $Down) {

        if ($Up >= 80) {
          return 1;
        } else {
          return 0;
        }

      } else {
        if ($Down >= 80) {
          return -1;
        } else {
          return 0;
        }
      }


    }



}

<?php
namespace App;

use Log;
use ReflectionClass;
use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\Trader;
use App\Candles;

class Indicators
{


  public $SMALL_FLOAT_PRECISION = 100000;

  /*
  Get moving average constant value from TALib class
  *
  *
  */
  public function getMovingAverageType($type) {
    $r = new ReflectionClass('LupeCode\phpTraderNative\TALib\Enum\MovingAverageType');
    $ma_type = $r->getConstant($type);
    if($ma_type !== false){
        return $ma_type;
    } else {
      return MovingAverageType::SMA; //default if other type not found.
    }
  }



  /*
  * Calculate bbands for data series
  * https://www.tradingview.com/wiki/Bollinger_Bands_(BB)
  *
  * return array data series with additional columns prefixed with $prefix.
  *
  [
    'type'=>'bbands',
    'prefix'=>'bbands10',
    'period'=>10,
    'StDevUp'=>2,
    'StDevDown'=>2,
    'MovingAverageType'=>'SMA',
    'Source'=>'close' // any of the values in the data series
  ]
  *
  */
  public function bbands($market_data, $spec, $signal = false) {
    //Log::debug('Adding bbands with prefix: ' . $spec['prefix']);
    $values = array();
    foreach ($market_data as $row) {
      $values[] = $row[$spec['Source']] * $this->SMALL_FLOAT_PRECISION;
    }
    $bbands = Trader::bbands(
                      $values,
                      $spec['period'],
                      $spec['StDevUp'],
                      $spec['StDevDown'],
                      $this->getMovingAverageType($spec['MovingAverageType']));

    if ($signal) {
      $output = [
        'Upperband'=>[],
        'MiddleBand'=>[],
        'UpperBand'=>[]
      ];
      foreach ($bbands['UpperBand'] as $key => $value) {
        $output['UpperBand'][$key] = $bbands['UpperBand'][$key] / $this->SMALL_FLOAT_PRECISION;
        $output['MiddleBand'][$key] = $bbands['MiddleBand'][$key] / $this->SMALL_FLOAT_PRECISION;
        $output['LowerBand'][$key] = $bbands['LowerBand'][$key] / $this->SMALL_FLOAT_PRECISION;
      }
      return $output;
    }
    //add the bbands to the dataset with prefix columns.
    for ($i=0; $i < count($market_data) ; $i++) {
      if ($i<$spec['period']) {
        $UpperBand = 0;
        $MiddleBand = 0;
        $LowerBand = 0;
      } else {
        $UpperBand = $bbands["UpperBand"][$i] / $this->SMALL_FLOAT_PRECISION;
        $MiddleBand = $bbands["MiddleBand"][$i] / $this->SMALL_FLOAT_PRECISION;
        $LowerBand = $bbands["LowerBand"][$i] / $this->SMALL_FLOAT_PRECISION;
      }
      $market_data[$i][$spec['prefix'] . 'UpperBand'] = $UpperBand;
      $market_data[$i][$spec['prefix'] . 'MiddleBand'] = $MiddleBand;
      $market_data[$i][$spec['prefix'] . 'LowerBand'] = $LowerBand;
    }


    return $market_data;

  }











  /*
  * Stochastic RSI
  *
  https://www.tradingview.com/wiki/Stochastic_RSI_(STOCH_RSI)

  [
    'type'=>'stochrsi',
    'prefix'=>'stochrsi',
    'period'=>14,
    'Kperiod'=>3,
    'Dperiod'=>3,
    'MovingAverageType'=>'SMA',
    'Source'=>'close'

  ],
  */
  public function stochrsi($market_data, $spec) {
    //Log::debug('Adding stochrsi with prefix: ' . $spec['prefix']);

    $values = array();
    foreach ($market_data as $row) {
      $values[] = $row[$spec['Source']];
    }
    $stochrsi = Trader::stochrsi(
                      $values,
                      $spec['period'],
                      $spec['Kperiod'],
                      $spec['Dperiod'],
                      $this->getMovingAverageType($spec['MovingAverageType']));

    //only start mapping from when fastK and D are available in the data series.
    reset($stochrsi['FastK']);
    $first_key = key($stochrsi['FastK']);



    for ($i=0; $i < count($market_data) ; $i++) {
        if ($i<$first_key) {
          $FastK = 0;
          $FastD = 0;
        } else {
          $FastK = $stochrsi['FastK'][$i];
          $FastD = $stochrsi['FastD'][$i];
        }
        $market_data[$i][$spec['prefix'] . 'FastK'] = $FastK;
        $market_data[$i][$spec['prefix'] . 'FastD'] = $FastD;
    }



    return $market_data;
  }






  public function stoch($market_data,$spec) {
    $high = array_column($market_data, 'high');
    $low = array_column($market_data, 'low');
    $close = array_column($market_data, 'close');

    $stoch = Trader::stoch(
                    $high,
                    $low,
                    $close,
                    $spec['Kperiod'],
                    $spec['Ksmoothing'],
                    $this->getMovingAverageType($spec['KMovingAverageType']),
                    $spec['Dsmoothing'],
                    $this->getMovingAverageType($spec['DMovingAverageType'])
                  );

                  reset($stoch['SlowK']);
                  $first_key = key($stoch['SlowK']);



                  for ($i=0; $i < count($market_data) ; $i++) {
                      if ($i<$first_key) {
                        $SlowK = 0;
                        $SlowD = 0;
                      } else {
                        $SlowK = $stoch['SlowK'][$i];
                        $SlowD = $stoch['SlowD'][$i];
                      }
                      $market_data[$i][$spec['prefix'] . 'K'] = $SlowK;
                      $market_data[$i][$spec['prefix'] . 'D'] = $SlowD;
                  }



                  return $market_data;


  }




  /*
  * Chaikin oscillator
  *
  * https://www.tradingview.com/wiki/Chaikin_Oscillator
  *
  *
  *
  [
    'type'=>'adosc',
    'prefix'=>'adosc',
    'fastLength'=>3,
    'slowLength'=>10
  ]
  *
  */
  public function adosc($market_data, $spec) {
    //Log::debug('Adding adosc (chaikin osc) with prefix: ' . $spec['prefix']);

    $high = array_column($market_data, 'high');
    $low = array_column($market_data, 'low');
    $close = array_column($market_data, 'close');
    $volume = array_column($market_data, 'volume');

    $adosc = Trader::adosc(
                    $high,
                    $low,
                    $close,
                    $volume,
                    $spec['fastLength'],
                    $spec['slowLength']
                  );

                  reset($adosc);
                  $first_key = key($adosc);

                  for ($i=0; $i < count($market_data) ; $i++) {
                      if ($i<$first_key) {
                        $adosc_value = 0;
                      } else {
                        $adosc_value = $adosc[$i];
                      }
                      $market_data[$i][$spec['prefix'] . 'ADOSC'] = $adosc_value;
                  }



    return $market_data;

  }



  /*
  * CCI Commodity Channel Index
  * https://www.tradingview.com/wiki/Commodity_Channel_Index_(CCI)
  *
  *
  [
    'type'=>'cci',
    'prefix'=>'cci',
    'length'=>'14'
  ]
  *
  */
  public function cci($market_data, $spec) {
    //Log::debug('Adding cci (Commodity Channel Index) with prefix: ' . $spec['prefix']);

    $high = array_column($market_data, 'high');
    $low = array_column($market_data, 'low');
    $close = array_column($market_data, 'close');
    $volume = array_column($market_data, 'volume');

    $cci = Trader::cci(
                    $high,
                    $low,
                    $close,
                    $spec['length']
                  );

                  reset($cci);
                  $first_key = key($cci);

                  for ($i=0; $i < count($market_data) ; $i++) {
                      if ($i<$first_key) {
                        $cci_value = 0;
                      } else {
                        $cci_value = $cci[$i];
                      }
                      $market_data[$i][$spec['prefix'] . 'CCI'] = $cci_value;
                  }

      return $market_data;

  }




  /*
  * MACD
  *
  [
    'type'=>'macd',
    'prefix'=>'macd',
    'fastLength'=>12,
    'slowLength'=>26,
    'smoothing'=>9,
    'Source'=>'close'
  ]
  */

  public function macd($market_data,$spec, $signal = false) {
    //Log::debug('Adding MACD with prefix: ' . $spec['prefix']);
    $values = array_column($market_data,$spec['Source']);
    //Log::debug(print_r($values,true));
    $macd = Trader::macd(
      $values,
      $spec['fastLength'],
      $spec['slowLength'],
      $spec['smoothing']
    );
    //dump($macd);

    //Log::debug(print_r($macd,true));
    reset($macd['MACD']);
    $first_key = key($macd['MACD']);


    for ($i=0; $i < count($market_data) ; $i++) {
        if ($i<$first_key) {
          $macd_MACD = 0;
          $macd_MACDSignal = 0;
          $macd_MACDHist = 0;
        } else {
          $macd_MACD = $macd['MACD'][$i];
          $macd_MACDSignal = $macd['MACDSignal'][$i];
          $macd_MACDHist = $macd['MACDHist'][$i];
        }
        $market_data[$i][$spec['prefix'] . 'MACDMACD'] = $macd_MACD;
        $market_data[$i][$spec['prefix'] . 'MACDSignal'] = $macd_MACDSignal;
        $market_data[$i][$spec['prefix'] . 'MACDHist'] = $macd_MACDHist;
    }

    if ($signal) {
      $return = [
        'macd' => array_column($market_data, $spec['prefix'] . 'MACDMACD'),
        'signal'=> array_column($market_data, $spec['prefix'] . 'MACDSignal'),
        'hist'=> array_column($market_data, $spec['prefix'] . 'MACDHist'),
      ];
      //dump($return);
      return $return;
    }

    return $market_data;


  }




  /*
  Money flow index
  * https://www.tradingview.com/wiki/Money_Flow_(MFI)
  *
  *
  [
    'type'=>'mfi',
    'prefix'=>'mfi',
    'period'=>14,
  ],
  */
  public function mfi($market_data, $spec, $signal = false) {
    $high = array_column($market_data, 'high');
    $low = array_column($market_data, 'low');
    $close = array_column($market_data, 'close');
    $volume = array_column($market_data, 'volume');

    $mfi = Trader::mfi(
                    $high,
                    $low,
                    $close,
                    $volume,
                    $spec['period']
                  );
                  reset($mfi);
                  $first_key = key($mfi);

                  for ($i=0; $i < count($market_data) ; $i++) {
                      if ($i<$first_key) {
                        $mfi_value = 0;
                      } else {
                        $mfi_value = $mfi[$i];
                      }
                      $market_data[$i][$spec['prefix'] . 'MFI'] = $mfi_value;
                  }
              if ($signal) {
                return array_column($market_data,$spec['prefix'] . 'MFI');
              }
              return $market_data;
  }




  /*
  * Williams %R
  * https://www.tradingview.com/wiki/Williams_%25R_(%25R)
  *
  *
  [
    'type'=>'willr',
    'prefix'=>'willr',
    'period'=>14
  ]
  */
  public function willr($market_data, $spec, $signal = false) {
    $high = array_column($market_data, 'high');
    $low = array_column($market_data, 'low');
    $close = array_column($market_data, 'close');

    $willr = Trader::willr(
                    $high,
                    $low,
                    $close,
                    $spec['period']
                  );
                  reset($willr);
                  $first_key = key($willr);

                  for ($i=0; $i < count($market_data) ; $i++) {
                      if ($i<$first_key) {
                        $willr_value = 0;
                      } else {
                        $willr_value = $willr[$i];
                      }
                      $market_data[$i][$spec['prefix'] . 'R'] = $willr_value;
                  }
      if ($signal) {
        return $willr;
      }
      return $market_data;
  }






  /*
  * Aroon
  * https://www.tradingview.com/wiki/Aroon

  * The Aroon indicator was developed by Tushar Chande in 1995.
  *
  * Both the Aroon up and the Aroon down fluctuate between zero and 100, with values close to 100 indicating a strong trend, and zero indicating a weak trend.
  * The lower the Aroon up, the weaker the uptrend and the stronger the downtrend, and vice versa.
  * The main assumption underlying this indicator is that a stock's price will close at record highs in an uptrend, and record lows in a downtrend.
  *
  [
    'type'=>'aroon',
    'prefix'=>'aroon',
    'period'=>14,
  ]
  */
  public function aroon($market_data, $spec, $signal = false) {
    $high = array_column($market_data, 'high');
    $low = array_column($market_data, 'low');


    $aroon = Trader::aroon($high, $low, $spec['period']);
    reset($aroon['AroonUp']);
        $first_key = key($aroon['AroonUp']);

        for ($i=0; $i < count($market_data) ; $i++) {
            if ($i<$first_key) {
              $aroonup_value = 0;
              $aroondown_value = 0;
            } else {
              $aroonup_value = $aroon['AroonUp'][$i];
              $aroondown_value = $aroon['AroonDown'][$i];
            }
            $market_data[$i][$spec['prefix'] . 'Up'] = $aroonup_value;
            $market_data[$i][$spec['prefix'] . 'Down'] = $aroondown_value;
        }
    if ($signal) {
      return [
        'AroonUp'=>array_column($market_data,$spec['prefix'] . 'Up'),
        'AroonDown'=>array_column($market_data,$spec['prefix'] . 'Down')
      ];
    }
    return $market_data;
  }




  public function aroonosc($market_data, $spec, $signal = false) {

  }






}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ccxt;
use Carbon\Carbon;

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\Trader;

use App\Ohlcv;
use App\Indicators;
use Log;
use App\MathUtils;


class TestController extends Controller
{


    public function signals() {
      $array = [
        ['low'=>1],
        ['low'=>2],
        ['low'=>3],
        ['low'=>7],
        ['low'=>5],
        ['low'=>4],
        ['low'=>4]
      ];

      if (MathUtils::consecutiveLows($array,'low',3)) {
        dump('YES');
      } else {
        dump('NO');
      }
    }


    public function indicators() {


      $data = $this->backtestOHLCV();
      dump($data);
      $ind = new Indicators;
      $macd = $ind->macd($data,[
        'type'=>'macd',
        'prefix'=>'macd',
        'fastLength'=>12,
        'slowLength'=>26,
        'smoothing'=>9,
        'Source'=>'close'
      ]);
      dump($macd);



      $OHLCV_warmup = 30;
      $periods = 50;
      for ($select_records = 1+$OHLCV_warmup; $select_records <= $periods; $select_records++) {


              $TOTALRECORDS = Ohlcv::where([
                ['timeframe','=','15m'],
                ['market_id','=',12]
                ])->count();

              $skip_records = $TOTALRECORDS - $periods;

                Log::debug('[' . $TOTALRECORDS . '] skip ' . $skip_records . ', select ' . $select_records);


                //Log::debug('[BACKTEST START] ' . $account->name . ' [' . $account->exchange->name . '] ' . $activemarket->market->symbol . ' :Strategy = ' . $activestrategy->strategy->name .  '==================================' );




                //Log::debug('[BACKTEST END]   ' . $account->name . ' [' . $account->exchange->name . '] ' . $activemarket->market->symbol . ' :Strategy = ' . $activestrategy->strategy->name . '==================================');






        }








    }


    public function backtestOHLCV() {
      $ohlcv_data = Ohlcv::where([
                          ['market_id','=',12],
                          ['timeframe','=','15m']
                          ])
                          ->orderBy('utctimestamp','asc')
                          ->skip(450)
                          ->take(33)
                          ->get();

      $MARKET_data = array();
      foreach ($ohlcv_data as $data) {
        $MARKET_data[] = [
            'utctimestamp'=>$data->utctimestamp,
            'timestamp_datetime'=>$data->timestamp_datetime,
            'open'=>$data->open,
            'high'=>$data->high,
            'low'=>$data->low,
            'close'=>$data->close,
            'volume'=>$data->volume
        ];
      }

      return $MARKET_data;

    }




      public function loadOHLCV() {
        $ohlcv_data = Ohlcv::where([
                            ['market_id','=',12],
                            ['timeframe','=','15m']
                            ])
                            ->orderBy('utctimestamp','desc')
                            ->limit(250)
                            ->get();
        //reverse the collection to show the lates
        $reversed = $ohlcv_data->reverse();

        //prepare the master data.
        $MARKET_data = array();
        foreach ($reversed as $data) {
          $MARKET_data[] = [
              'utctimestamp'=>$data->utctimestamp,
              'timestamp_datetime'=>$data->timestamp_datetime,
              'open'=>$data->open,
              'high'=>$data->high,
              'low'=>$data->low,
              'close'=>$data->close,
              'volume'=>$data->volume
          ];
        }

        return $MARKET_data;

      }







    public function test() {
      //$api = new binance("ZpjCNIJQnasRFAOgd8rshgKATDfzlYfjMNpSqLpdLiXaL9BCdIdUVvoCAQiYIJo0","DG2lF8m8Mo2Zn6hbEiywE56wwtJ55jyvMgXvbVnndU6flCwsORTxDwfU0uwldFIB");
      //dump (\ccxt\Exchange::$exchanges);
      //$id = 'huobi';
      $binance = new \ccxt\binance();
      //dump($binance->rateLimit);
      //$markets = $binance->load_markets();
      //dump($binance->timeframes);
      //return json_encode($markets);

      //$tickers = $binance->fetch_tickers();
      //return json_encode($tickers);

      //dump($binance->timeframes);
      //$ohlcv = $binance->fetch_ohlcv ('NEO/ETH', '1m', 1520467740000); // pair, timeframe, optional [since timestamp]
      //dump ($this->bbands($ohlcv));
      //dump($ohlcv);


      /*$bbands = $this->bbands($ohlcv);



      //TEST STREAM FORMATTING
      //
      $data = array();
      $header = [
        'open',
        'high',
        'low',
        'close',
        'volume',
        'bband_upper',
        'bband_mid',
        'bband_lower'
      ];
      $data[] = $header;

      $ohlcv_length = count($ohlcv);
      for ($i=0; $i < $ohlcv_length ; $i++) {
        if ($i<10) {
          $bband_upper = 0;
          $bband_mid = 0;
          $bband_lower = 0;
        } else {
          $bband_upper = $bbands["UpperBand"][$i];
          $bband_mid = $bbands["MiddleBand"][$i];
          $bband_lower = $bbands["LowerBand"][$i];
        }

        $row = [
          $ohlcv[$i][1],
          $ohlcv[$i][2],
          $ohlcv[$i][3],
          $ohlcv[$i][4],
          $ohlcv[$i][5],
          $bband_upper,
          $bband_mid,
          $bband_lower,
        ];
        $data[] = $row;
      }


      return json_encode($data);

      */

      //$stockrsi = $this->stochrsi($ohlcv);
      //dump($stockrsi);
      //return json_encode($stockrsi);


      //login
      $binance->apiKey = 'ZpjCNIJQnasRFAOgd8rshgKATDfzlYfjMNpSqLpdLiXaL9BCdIdUVvoCAQiYIJo0';
      $binance->secret = 'DG2lF8m8Mo2Zn6hbEiywE56wwtJ55jyvMgXvbVnndU6flCwsORTxDwfU0uwldFIB';
      dump ($binance->fetch_balance());

      //dump ($binance->fetchOrders('NEO/BTC'));
      //dump ($binance->fetchMyTrades('NEO/BTC'));

    }



    //Test stoch RSI
    //
    //
    private function stochrsi($ohlcv_data) {
      $values = array();
      foreach ($ohlcv_data as $data) {
        $values[] = $data[4]; //closing data
      }
      $stochrsi = Trader::stochrsi($values,14,3,3);
      return $stochrsi;
      /*
      array[
        FastK[array]
        FastD[array]
      ]
      */
    }





    //Test function to generate bbands on standarf ohlcv data.
    //
    private function bbands($ohlcv_data) {
      $values = array();
      foreach ($ohlcv_data as $data) {
        $values[] = $data[4]; //closing data
      }
      $bbands = Trader::bbands($values, 10, 2,2,MovingAverageType::SMA);  //0 is SMA type
      return $bbands;
      /*
      array() [UpperBand][array], [MiddleBand][array], [LowerBand][array]


      */
    }



    /**
     * @param array $outReal
     * @param int   $precision
     * @param int   $mode
     *
     * @return array
     */
    protected function adjustForPECL(array $outReal, int $precision = 3, int $mode = \PHP_ROUND_HALF_DOWN)
    {
        $newOutReal = [];
        foreach ($outReal as $index => $inDouble) {
            $newOutReal[$index] = round($inDouble, $precision, $mode);
        }

        return $newOutReal;
    }

}

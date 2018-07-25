<?php
//Data ingestor for exchange markets
//
//
//
//
namespace App;

use DB;
use App\Market;
use App\Exchange;
use App\Ohlcv;
use Log;

use ccxt;
use Carbon\Carbon;

class OhlcvIngestor
{

  public function loadData(Market $market, $timeframe) {
    Log::debug('Ingesting OHLCV data for ' . $market->symbol . ' [' . $market->exchange->name . ']');

    switch ($market->exchange->name) {
      case 'Binance':
        $this->loadBinanceData($market, $timeframe);
        break;

      default:
        Log::warning('No OHLCV ingestor setup for ' . $exchange->name);
        break;
    }
  }




  //BINANCE
  //
  //
  //
  //
  //


  public function loadBinanceData($market, $timeframe) {
    $binance = new \ccxt\binance();
    usleep ($binance->rateLimit * 1000); // make sure we stay within the rate limits for the exchange
    //1.check timespan is supported by this exchange
    if (array_key_exists($timeframe, $binance->timeframes)) {
      //2.Load last entry to get starting timestamp, otherwise load all.
      $data = DB::table('ohlcvs')->where([
        ['market_id','=',$market->id],
        ['timeframe','=',$timeframe]
      ])->limit(1)->orderBy('utctimestamp','desc')->get();

      if ($data->isEmpty()) {
        Log::debug('Loading full OHLCV data for ' . $market->symbol . ' [' . $market->exchange->name . '] ' . $timeframe);
        $this->loadFullDataBinance($market, $timeframe);
      } else {
        Log::debug('Loading recent OHLCV data for ' . $market->symbol . ' [' . $market->exchange->name . '] ' . $timeframe);
        //Log::debug('getting since timestamp ' . $data[0]->utctimestamp);
        $this->loadRecentDataBinance($market, $timeframe, $data[0]);
      }

    } else {
      Log::error('Timespan of ' . $timeframe . ' cannot be used with Binance exchange');
    }
  }




  private function loadFullDataBinance($market,$timeframe) {
    $binance = new \ccxt\binance();
    $ohlcv = $binance->fetch_ohlcv($market->symbol, $timeframe);
    foreach ($ohlcv as $data) {
      $ohlcv_record = new Ohlcv;
      $ohlcv_record->market_id = $market->id;
      $ohlcv_record->utctimestamp = $data[0];
      $ohlcv_record->timestamp_datetime = Carbon::createFromTimestampMs($data[0])->toDateTimeString();
      $ohlcv_record->open = $data[1];
      $ohlcv_record->high = $data[2];
      $ohlcv_record->low = $data[3];
      $ohlcv_record->close = $data[4];
      $ohlcv_record->volume = $data[5];
      $ohlcv_record->timeframe = $timeframe;
      $ohlcv_record->save();
    }

  }

  private function loadRecentDataBinance($market,$timeframe,$lastrecord) {
    $binance = new \ccxt\binance();
    $ohlcv = $binance->fetch_ohlcv($market->symbol, $timeframe, $lastrecord->utctimestamp);

    foreach ($ohlcv as $data) {
      if ($data[0] > $lastrecord->utctimestamp ) {
        $ohlcv_record = new Ohlcv;
        $ohlcv_record->market_id = $market->id;
        $ohlcv_record->utctimestamp = $data[0];
        $ohlcv_record->timestamp_datetime = Carbon::createFromTimestampMs($data[0])->toDateTimeString();
        $ohlcv_record->open = $data[1];
        $ohlcv_record->high = $data[2];
        $ohlcv_record->low = $data[3];
        $ohlcv_record->close = $data[4];
        $ohlcv_record->volume = $data[5];
        $ohlcv_record->timeframe = $timeframe;
        $ohlcv_record->save();
      }
    }
  }







}

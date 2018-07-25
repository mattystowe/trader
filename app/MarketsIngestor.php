<?php
//Data ingestor for exchange markets
//
//
//
//
namespace App;

use App\Market;
use App\Exchange;
use Log;

use ccxt;
use Carbon\Carbon;

class MarketsIngestor
{

  public function getMarkets(Exchange $exchange) {
    Log::debug('Ingesting Markets for ' . $exchange->name);

    switch ($exchange->name) {
      case 'Binance':
        $this->loadBinanceMarkets($exchange);
        break;

      default:
        Log::warning('No markets ingestor setup for ' . $exchange->name);
        break;
    }
  }




  public function loadBinanceMarkets($exchange) {
    $binance = new \ccxt\binance();
    $markets = $binance->load_markets();
    Log::debug('Found [' . count($markets) . '] Available Markets for ' . $exchange->name);

    //Update or save new market pair
    //
    foreach ($markets as $market) {
      $market = Market::updateOrCreate(
          ['exchange_id' => $exchange->id, 'symbol'=>$market['symbol']],
          [
            'symbolid' => $market['symbol'],
            'base_symbol' => $market['base'],
            'quote_symbol' => $market['quote'],
            'min_amount' => $market['limits']['amount']['min'],
            'min_price' => $market['limits']['price']['min'],
            'min_cost' => $market['limits']['cost']['min']
          ]
      );
    }

  }

}

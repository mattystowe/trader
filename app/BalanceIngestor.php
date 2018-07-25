<?php
//Data ingestor for exchange markets
//
//
//
//
namespace App;

use App\Market;
use App\Exchange;
use App\Account;
use App\Balance;
use Log;

use ccxt;
use Carbon\Carbon;

class BalanceIngestor
{

  public function getBalances(Account $account) {
    Log::debug('Loading Balances for ' . $account->name);

    switch ($account->exchange->name) {
      case 'Binance':
        $this->loadBinanceBalances($account);
        break;
      //
      //
      //Add more exchanges here
      //
      //
    }
  }




  public function loadBinanceBalances($account) {

    $binance = new \ccxt\binance();
    $binance->apiKey = $account->apikey;
    $binance->secret = $account->secret;
    usleep ($binance->rateLimit * 1000);
    
    $balance_bucket = $binance->fetch_balance();


    //Update or save balance
    //
    foreach ($balance_bucket as $symbol=>$data) {
      if ($symbol != 'info' && $symbol != 'free'  && $symbol != 'used' && $symbol != 'total') {
        $balance = Balance::updateOrCreate(
            ['account_id' => $account->id, 'symbol'=>$symbol],
            [
              'free' => $data['free'],
              'used' => $data['used'],
              'total' => $data['total']
            ]
        );

      }


    }


  }






}

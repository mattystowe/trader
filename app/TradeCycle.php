<?php

namespace App;

use Log;
use App\Account;
use App\Strategy;
use App\Activestrategy;

class TradeCycle
{







  //Main route into the trade cycle.
  //
  //
  //
  public function execute() {
    //
    //Do stuff here if needed before cycle.
    //
    $this->cycle();
  }
  //
  //
  //






  //Main cycle digest loop
  //
  //
  public function cycle() {
    $accounts = Account::all();

    //Loop all accounts
    //
    foreach ($accounts as $account) {

          //For each account - loop all active markets for account
            //
          foreach ($account->activemarkets as $activemarket) {
            if ($activemarket->isActive()) {

                //For each active market - send to all active strategies on account
                //
                foreach ($account->activestrategies as $activestrategy) {
                  //Log::debug('[START] ' . $account->name . ' [' . $account->exchange->name . '] ' . $activemarket->market->symbol . ' :Strategy = ' . $activestrategy->strategy->name .  '==================================' );

                  if ($activestrategy->isActive()) {
                    $strategy_class = 'App\Strategies\\' . $activestrategy->strategy->classinstance;
                    $strategy = new $strategy_class($activestrategy, $activemarket);
                    $strategy->think();
                  }

                  //Log::debug('[END]   ' . $account->name . ' [' . $account->exchange->name . '] ' . $activemarket->market->symbol . ' :Strategy = ' . $activestrategy->strategy->name . '==================================');
                }

            }
          }
    }
  }


}

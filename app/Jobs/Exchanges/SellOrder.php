<?php

namespace App\Jobs\Exchanges;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Account;
use App\Balance;
use App\Journal;
use Log;

class SellOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $position;
    public $exchange;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\App\Position $position)
    {
        $this->position = $position;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


      //Set position
      $position = $this->position;


      //set up the exchange
      $exchange_classname = '\ccxt\\' . $position->activemarket->account->exchange->classinstance;
      $this->exchange = new $exchange_classname();


      //Get balances
      $position->activemarket->account->loadRealTimeBalances();

      $quote_currency_balance = $position->activemarket->account->getFreeBalance($position->activemarket->market->quote_symbol);
      $base_currency_balance = $position->activemarket->account->getFreeBalance($position->activemarket->market->base_symbol);


      $base_currency_quantity = $base_currency_balance; // 100% of base currency is sold during a sell order. (no pyramiding orders)
      $quote_currency_quantity = $base_currency_quantity * $position->exit_price;
      $order_fee = $quote_currency_quantity * $position->activemarket->account->exchange->fee;



      Log::debug('Handle Sell Order ' . $position->activemarket->market->symbol . ' Sell ' . $base_currency_quantity . ' ' . $position->activemarket->market->base_symbol . ' back for ' . $quote_currency_quantity . ' ' . $position->activemarket->market->quote_symbol . ' with fee of ' . $order_fee . ' ' . $position->activemarket->market->quote_symbol);






            //
            //
            //
            //
            //TODO - Execute Exchange Order, and save a new Orders to the position.
            //
            //
            //
            //
            //



            //record fees in ledger
            //
            $journal = new Journal;
            $journal->position_id = $position->id;
            $journal->journal_type = 'fee';
            $journal->fee = $order_fee;
            $journal->loss = 0.00;
            $journal->profit = 0.00;
            $journal->currency = $position->activemarket->market->quote_symbol;
            $journal->posting_date = $position->exit_time;
            $journal->save();




            //record profit/loss in ledger
            //
            //(positions.exit_price - positions.entry_price ) as gain,
            //((positions.exit_price - positions.entry_price )  / positions.entry_price) * 100 as percent

            $journal = new Journal;
            $journal->position_id = $position->id;
            $percent_change = (($position->exit_price - $position->entry_price) / $position->entry_price) * 100;
            $amount = ($position->trade / 100) * $percent_change;

            if ($position->exit_price < $position->entry_price) {
              $journal->journal_type = 'loss';
              $journal->loss = -$amount; // always store in positive values for reporting
              $journal->profit = 0.00;
              $journal->fee = 0.00;
            } else {
              $journal->journal_type = 'profit';
              $journal->profit = $amount;
              $journal->loss = 0.00;
              $journal->fee = 0.00;
            }

            $journal->currency = $position->activemarket->market->quote_symbol;
            $journal->posting_date = $position->exit_time;
            $journal->save();



            //Backtest update balances
            //
            //
            //update free balance on quote_currency
            $new_balance = $quote_currency_balance + ($quote_currency_quantity - $order_fee);
            $position->activemarket->account->updateFreeBalance($position->activemarket->market->quote_symbol,$new_balance);
            //
            //
            //update free balance on base_currency
            $new_balance = $base_currency_balance - $base_currency_quantity;
            $position->activemarket->account->updateFreeBalance($position->activemarket->market->base_symbol,$new_balance);
            //
            //
            ////////////////////////////




    }
}

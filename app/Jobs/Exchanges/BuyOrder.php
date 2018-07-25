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

class BuyOrder implements ShouldQueue
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

      $trade = $quote_currency_balance * $position->activestrategy->order_commitment_level;
      $orderQuantity = $trade / $position->entry_price;
      $order_fee = $orderQuantity * $position->activemarket->account->exchange->fee;

      Log::debug('Handle Buy Order ' . $position->activemarket->market->symbol . ' Trade ' . $trade . ' ' . $position->activemarket->market->quote_symbol . ' for ' . $orderQuantity . ' ' . $position->activemarket->market->base_symbol . ' with fee of ' . $order_fee . ' ' . $position->activemarket->market->base_symbol);



      //check that quantity is over $market->min_amount
      //
      //
      if ($orderQuantity >= $position->activemarket->market->min_amount) {




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


            //
            //record fees in ledger
            //
            $journal = new Journal;
            $journal->position_id = $position->id;
            $journal->journal_type = 'fee';
            $journal->fee = $order_fee;
            $journal->profit = 0.00;
            $journal->loss = 0.00;
            $journal->currency = $position->activemarket->market->base_symbol;
            $journal->posting_date = $position->entry_time;
            $journal->save();


            //Backtest update balances
            //
            //
            //update free balance on quote_currency
            $new_balance = $quote_currency_balance - $trade;
            $position->activemarket->account->updateFreeBalance($position->activemarket->market->quote_symbol,$new_balance);
            //
            //
            //
            //update free balance on base_currency
            $new_balance = $base_currency_balance + $orderQuantity - $order_fee;
            $position->activemarket->account->updateFreeBalance($position->activemarket->market->base_symbol,$new_balance);
            //
            //
            ////////////////////////////



            //update position with trade amount spent
            $position->trade = $trade;
            $position->save();


      } else {
        //
        //
        //Order under minimum amount
        Log::debug('Trade not possible - Underneath minimum order amount.');
      }


    }
}

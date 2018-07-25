<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Activestrategy;
use App\Activemarket;
use App\Account;
use App\Market;
use App\Ohlcv;
use Log;
use DB;

class Backtest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trader:backtest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backtest system strategies.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $accounts = Account::all();
        foreach($accounts as $account) {
          $this->line('[' . $account->id . '] ' . $account->name . ' [' . $account->exchange->name . ']');
        }
        $account_id = $this->ask('Select account to test on');

        $account = Account::find($account_id);

        foreach ($account->activestrategies as $activestrategy) {
          $this->line('[' . $activestrategy->id . '] ' . $activestrategy->strategy->name);
        }
        $activestrategy_id = $this->ask('Select strategy to test on');
        $activestrategy = Activestrategy::find($activestrategy_id);


        //present periods available for markets
        $periods = $this->ask('Backtest for how many periods into the past?');
        $periods_check = true;
        foreach ($account->activemarkets as $activemarket) {
          $p = Ohlcv::where([
            ['timeframe','=',$activestrategy->strategy->timeframe],
            ['market_id','=',$activemarket->market_id]
            ])->count();
            if ($p < $periods) {
              $periods_check = false;
              $this->error($activemarket->market->symbol . ' - ' . $p . ' available.');
            } else {
              $this->line($activemarket->market->symbol . ' - ' . $p . ' available.');
            }

        }





        if ($periods_check) {

          //warn user that we will blat the positions and tracer tables
          $confirm = $this->ask('Y or N - HERE BE DRAGONS - are you sure?  This simulation will BLAT the positions table and reset the backtest tracer table.');

          if (strtoupper($confirm) == 'Y') {
          DB::table('positions')->truncate();
          DB::table('tracers')->truncate();
          DB::table('journals')->truncate();
          $this->runbacktest($account, $activestrategy, $periods);

          } else {
              $this->info('Fair enough - if you want to play with the big boys.. then you grow a pair.');
          }

        } else {
          $this->info('Please load in more data for the symbol indicated above');
        }



    }








    public function runbacktest($account, $activestrategy, $periods) {
      //For each account - loop all active markets for account
      //

        $OHLCV_warmup = 34; // minimum data points for ohlcv data to be useful
        $bar = $this->output->createProgressBar($periods - $OHLCV_warmup);
        for ($select_records = $OHLCV_warmup; $select_records <= $periods; $select_records++) {


              foreach ($account->activemarkets as $activemarket) {
                if ($activemarket->isActive()) {


                  $TOTALRECORDS = Ohlcv::where([
                    ['timeframe','=',$activestrategy->strategy->timeframe],
                    ['market_id','=',$activemarket->market_id]
                    ])->count();

                  $skip_records = $TOTALRECORDS - $periods;

                    //$this->info('[' . $TOTALRECORDS . ']' . $activemarket->market->symbol . ' skip ' . $skip_records . ', select ' . $select_records);


                    //Log::debug('[BACKTEST START] ' . $account->name . ' [' . $account->exchange->name . '] ' . $activemarket->market->symbol . ' :Strategy = ' . $activestrategy->strategy->name .  '==================================' );

                    $strategy_class = 'App\Strategies\\' . $activestrategy->strategy->classinstance;
                    $strategy = new $strategy_class($activestrategy, $activemarket, true, $skip_records, $select_records);
                    $strategy->think();


                    //Log::debug('[BACKTEST END]   ' . $account->name . ' [' . $account->exchange->name . '] ' . $activemarket->market->symbol . ' :Strategy = ' . $activestrategy->strategy->name . '==================================');

                }

              }

            //usleep (50000);
            $bar->advance();

          }

          $bar->finish();

    }



}

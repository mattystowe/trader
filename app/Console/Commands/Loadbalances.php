<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Balance;
use App\Account;
use App\BalanceIngestor;
use Log;

class Loadbalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trader:loadbalances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads exchange balances';

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
        foreach ($accounts as $account) {
          $balanceLoader = new BalanceIngestor;
          $balanceLoader->getBalances($account);
        }
    }
}

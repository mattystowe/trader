<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class StartTrading extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trader:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show me the money!';

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
      $this->info('HODLing is for losers...lets make some money!');

        while(1) {
          Artisan::call('schedule:run');
          $this->info('Trader digest completed...');
          sleep(30);
        }
    }
}

<?php
//Command to load in markets for all exchanges in the system.
//
//Command run daily.
//
//
//
//
//
//
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exchange;
use App\Market;
use App\MarketsIngestor;
use Log;

class LoadMarkets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trader:loadmarkets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads in and updates the list of available markets for each exchange.';

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
        $exchanges = Exchange::all();
        $MarketsIngestor = new MarketsIngestor;
        foreach ($exchanges as $exchange) {
          $this->info('Loading markets for exchange: ' . $exchange->name);
          $MarketsIngestor->getMarkets($exchange);
        }
    }

}

<?php
//Command to load in OHLCV (Opening, High, Low, Close) data for all *active* markets.
//
//Command run according to frequency specified in input.
//
//
/*
timeframes generally available from $exchange->timeframes property.
  Eg
  "1m" => "1m"
  "3m" => "3m"
  "5m" => "5m"
  "15m" => "15m"
  "30m" => "30m"
  "1h" => "1h"
  "2h" => "2h"
  "4h" => "4h"
  "6h" => "6h"
  "8h" => "8h"
  "12h" => "12h"
  "1d" => "1d"
  "3d" => "3d"
  "1w" => "1w"
  "1M" => "1M"


*/
//
//
//
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Exchange;
use App\Market;
use App\Activemarket;
use App\MarketsIngestor;
use App\OhlcvIngestor;
use Log;

class LoadOHLCV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trader:loadOHLCV {timeframe : The time frame for loading data. 1m, 5m, 15m, 30m, 1h, 2h, 4h, 6h, 8h, 12h, 1d, 3d, 1w, 1M }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'load in OHLCV (Opening, High, Low, Close) data for all *active* markets.';

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
        $OHLCVIngestor = new OhlcvIngestor;

        $am = Activemarket::all();
        $activemarkets = [];
        foreach ($am as $m) {
          if ($m->isActive()) {
            $activemarkets[] = $m->market_id;
          }
        }
        $active_markets = array_unique($activemarkets);




        //$activemarkets = DB::table('activemarkets')->select('market_id')->groupBy('market_id')->get();
        if (count($active_markets)>0) {
          foreach ($active_markets as $activemarket) {
              $market = Market::find($activemarket);
              $this->info('Loading OHLCV data for ' . $market->symbol . ' [' . $market->exchange->name . ']');
              $OHLCVIngestor->loadData($market,$this->argument('timeframe'));
          }
        } else {
          $this->info('No active markets configured');
        }

    }
}

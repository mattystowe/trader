<?php
//Process the trade cycle
//
//
//
//
//
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\TradeCycle;

class ProcessTradeCycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trader:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the trade cycle';

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
        $this->info('Processing Cycle');
        Log::debug('////////START CYCLE /////////////////////////////////////////////////');

        $TradeCycle = new TradeCycle;
        $TradeCycle->execute();

        Log::debug('////////END CYCLE ///////////////////////////////////////////////////');
    }
}

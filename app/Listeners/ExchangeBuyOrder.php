<?php

namespace App\Listeners;

use App\Events\PositionOpened;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class ExchangeBuyOrder
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PositionOpened  $event
     * @return void
     */
    public function handle(PositionOpened $event)
    {
        Log::debug('EVENT HANDLER: Buy Order ');
        $job = (new \App\Jobs\Exchanges\BuyOrder($event->position))->onQueue(env('QUEUE_NAME'));
        dispatch($job);
    }
}

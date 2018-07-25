<?php

namespace App\Listeners;

use App\Events\PositionClosed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class ExchangeSellOrder
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
     * @param  PositionClosed  $event
     * @return void
     */
    public function handle(PositionClosed $event)
    {
        Log::debug('EVENT HANDLER: Sell Order ');
        $job = (new \App\Jobs\Exchanges\SellOrder($event->position))->onQueue(env('QUEUE_NAME'));
        dispatch($job);
    }
}

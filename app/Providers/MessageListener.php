<?php

namespace App\Providers;

use App\Providers\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageListener
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
     * @param  Message  $event
     * @return void
     */
    public function handle(Message $event)
    {
        //
    }
}

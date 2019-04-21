<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Bschmitt\Amqp\Facades\Amqp;
use App\Events\SendEmailRegister;

class SendEmailRegisterListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(SendEmailRegister $event)
    {
        Amqp::publish('', json_encode(['name' => 'EmailRegister', 'data' =>['user' => $event->user]]) , ['queue' => 'wallet-queue']);
    }
}

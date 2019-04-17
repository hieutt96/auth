<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Bschmitt\Amqp\Facades\Amqp;
use App\Notifications\SendEmailActiveNotification;
use App\User;

class AmqpConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amqp:console';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Amqp::consume('wallet-queue', function($message, $resolver){
            // Log::info(json_decode($message->body, true));
            $resolver->acknowledge($message);
            try {
                $event = json_decode($message->body, true);
                Log::info($event);
                if($event) {
                    switch ($event['name']) {
                        case 'EmailActive':
                            $this->info("Sending Email Active User notifications");
                            $userId = $event['data']['user']['id'];
                            $user = User::find($userId);
                            $this->info($user);
                            if($user) {
                                $user->notify(new SendEmailActiveNotification($user));
                            }
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
            }catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        });
    }
}

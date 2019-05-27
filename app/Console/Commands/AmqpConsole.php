<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Bschmitt\Amqp\Facades\Amqp;
use App\Notifications\SendEmailActiveNotification;
use App\User;
use App\Notifications\SendEmailRegisterNotification;
use App\Notifications\TransferSuccessNotification;
use App\Notifications\TransferCreateNotification;
use App\Notifications\RechargeSuccessNotification;
use App\Notifications\WithdrawalSuccessNotification;

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
            // $resolver->acknowledge($message);
            try {
                $event = json_decode($message->body, true);
                Log::info($event);
                if($event) {
                    switch ($event['name']) {
                        case 'EmailRegister':
                            $this->info("Sending Email Register User notifications");
                            $userId = $event['data']['user']['id'];
                            $user = User::find($userId);
                            $this->info($user);
                            if($user) {
                                $user->notify(new SendEmailRegisterNotification($user));
                                Log::info('Đã send notification');
                            }
                            break;
                        case 'TransferSuccess':
                            $this->info("Sending Transfer Success notifications");
                            $userFromId = $event['data']['account_from']['user_id'];
                            $userFrom = User::find($userFromId);

                            $userToId = $event['data']['account_to']['user_id'];
                            $userTo = User::find($userToId);

                            $amount = $event['data']['amount'];
                            if($userFrom) {

                                $userFrom->notify(new TransferCreateNotification($userFrom, $userTo, $amount));
                                Log::info('Đã send notification transfer success');
                            }
                            if($userTo) {
                                $userTo->notify(new TransferSuccessNotification($userFrom, $userTo, $amount));
                                Log::info('Đã send notification transfer create');
                            }
                            break;
                        case 'RechargeSuccess':
                            $this->info('Notification Recharge Success');
                            $userId = $event['data']['recharge']['user_id'];
                            $user = User::find($userId);
                            if($user) {
                                $recharge = $event['data']['recharge'];
                                $user->notify(new RechargeSuccessNotification($user, $recharge));
                                Log::info('Đã gửi notification nạp tiền thành công');
                            }
                            break;

                        case 'WithdrawalSuccess':
                            $this->info('Notification Withdrawal Success');
                            $userId = $event['data']['withdrawal']['user_id'];
                            $user = User::find($userId);
                            if($user) {
                                $withdrawal = $event['data']['withdrawal'];
                                $user->notify(new WithdrawalSuccessNotification($user, $withdrawal));
                                Log::info('Đã gửi notification rút tiền thành công');
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
            $resolver->acknowledge($message);
        }, [
            'persistent' => true,// required if you want to listen forever
        ]);
    }
}

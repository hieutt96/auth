<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RechargeSuccessNotification extends Notification
{

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $user;
    public $recharge;
    public function __construct($user, $recharge)
    {
        $this->user = $user;
        $this->recharge = $recharge;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $type = $this->recharge['type'];
        if($type == 1) {
            $typeName = 'VnPay';
        }elseif($type == 2) {
            $typeName = 'MoMo';
        }else {
            $typeName = '';
        }
        return (new MailMessage)
                    ->from('no-reply@mywallet.vn')
                    ->greeting('Xin chào '.$this->user->name . ',')
                    ->line('Bạn vừa nạp thành công số tiền '.number_format($this->recharge['amount'], 0, ',', '.').' từ '.$typeName);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $type = $this->recharge['type'];

        if($type == 1) {
            $typeName = 'VnPay';
        }elseif($type == 2) {
            $typeName = 'MoMo';
        }else {
            $typeName = '';
        }
        return [
            'data' => 'Bạn vừa nạp thành công số tiền '.number_format($this->recharge['amount'], 0, ',', '.').' từ  '.$typeName,
        ];
    }
}

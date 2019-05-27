<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WithdrawalSuccessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $user;
    public $withdrawal;
    public function __construct($user, $withdrawal)
    {
        $this->user = $user;
        $this->withdrawal = $withdrawal;
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
        $type = $this->withdrawal['type'];
        if($type == 1) {
            $typeName = 'VnPay';
        }elseif($type == 2) {
            $typeName = 'MoMo';
        }else {
            $typeName = '';
        }
        if($this->withdrawal['stat'] == 2) {
            $statName = 'Thành công';
        }else {
            $statName = 'Thất bại';
        }
        return (new MailMessage)
                    ->from('no-reply@mywallet.vn')
                    ->greeting('Xin chào '.$this->user->name . ',')
                    ->line('Bạn vừa rút số tiền '.number_format($this->withdrawal['amount'], 0, ',', '.').' từ '.$typeName.' trạng thái '.$statName);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $type = $this->withdrawal['type'];

        if($type == 1) {
            $typeName = 'VnPay';
        }elseif($type == 2) {
            $typeName = 'MoMo';
        }else {
            $typeName = '';
        }
        return [
            'data' => 'Bạn vừa rút thành công số tiền '.number_format($this->withdrawal['amount'], 0, ',', '.').' từ  '.$typeName,
        ];
    }
}

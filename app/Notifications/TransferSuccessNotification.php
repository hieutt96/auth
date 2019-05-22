<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;

class TransferSuccessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $userFrom;
    public $userTo;
    public $amount;

    public function __construct($userFrom, $userTo, $amount)
    {
        $this->userFrom = $userFrom;
        $this->userTo = $userTo;
        $this->amount = $amount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->from('no-reply@mywallet.vn')
                    ->greeting('Xin chào '.$this->userTo->name . ',')
                    ->line('Bạn vừa nhận được '.number_format($this->amount, 0, ',', '.').' từ '.$this->userFrom->name);
                    
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'data' => 'Bạn vừa nhận được số tiền '.$this->amount.' từ  '.$this->userFrom->name,
        ];
    }
}

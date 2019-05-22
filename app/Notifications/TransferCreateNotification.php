<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TransferCreateNotification extends Notification
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
        return ['mail', 'database'];
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
                    ->greeting('Xin chào '.$this->userFrom->name . ',')
                    ->line('Bạn vừa chuyển '.number_format($this->amount, 0, ',', '.').' cho '.$this->userTo->name);
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
           'data' => 'Bạn vừa chuyển số tiền '.$this->amount.' cho  '.$this->userTo->name,
        ];
    }
}

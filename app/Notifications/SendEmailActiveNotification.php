<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendEmailActiveNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $urlActive = base64_encode(json_encode($this->user));
        return (new MailMessage)
                    ->from('no-reply@mywallet.vn')
                    ->subject('Email kích hoạt tài khoản')
                    ->greeting('Xin chào '.$this->user->name . ',')
                    ->line('Bạn đã đăng ký tài khoản tại website của chúng tôi')
                    ->action('Click để kích hoạt tài khoản', env('WEB_URL','http://localhost:1002'.'/active?user='.$urlActive))
                    ->line('Thank you for using our application!');
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
            //
        ];
    }
}

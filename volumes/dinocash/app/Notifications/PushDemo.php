<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PushDemo extends Notification
{

    use Queueable;
    
    public function __construct(private string $message) {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title("Depósito recebido")
            ->body("Você recebeu um deposito de {$this->message}")
            ->icon('../../resources/pwa/apple-touch-icon.png')
            // ->action('View App', 'notification_action')
        ;
    }

}

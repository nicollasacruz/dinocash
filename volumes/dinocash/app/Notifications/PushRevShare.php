<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PushRevShare extends Notification
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
            ->title("Comissão recebida")
            ->body("Você recebeu um RevShare de {$this->message}")
            ->icon('../../resources/pwa/pwa-192x192.png')
            // ->action('View App', 'notification_action')
        ;
    }

}

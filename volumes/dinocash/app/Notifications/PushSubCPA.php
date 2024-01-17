<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PushSubCPA extends Notification
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
            ->body("Você recebeu um Sub CPA de {$this->message}")
            ->icon('../../resources/pwa/pwa-192x192.png')
            ->vibrate([200, 100, 200, 100, 200, 100, 200])
            ->tag('vibration-sample')
            // ->action('View App', 'notification_action')
        ;
    }

}

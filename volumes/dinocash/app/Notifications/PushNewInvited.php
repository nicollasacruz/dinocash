<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PushNewInvited extends Notification
{

    use Queueable;

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title("Novo usuário cadastrado")
            ->body("Um novo usuário criou uma conta usando seu link!")
            ->icon('../../resources/pwa/pwa-192x192.png')
            // ->action('View App', 'notification_action')
        ;
    }

}

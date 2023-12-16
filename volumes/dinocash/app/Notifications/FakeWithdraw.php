<?php

namespace App\Events;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class FakeWithdraw extends Notification
{
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title("Transferência recebida")
            ->body("Você recebeu uma transferência de de Suitpay Instituicao de Pagamentos Ltda.")
            ->icon('../../resources/pwa/nubank-apple-touch-icon.png')
            ->vibrate([200, 100, 200, 100, 200, 100, 200])
            ->tag('vibration-sample')
            ->options(['TTL' => 1000]);
            // ->action('View account', 'view_account')
            // ->data(['id' => $notification->id])
            // ->badge()
            // ->dir()
            // ->image()
            // ->lang()
            // ->renotify()
            // ->requireInteraction()
    }
}
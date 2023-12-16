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

  public function via($notifiable)
  {
    return [WebPushChannel::class];
  }

  public function toWebPush($notifiable, $notification)
  {
    return (new WebPushMessage)
      ->title("Transferência recebida")
      ->body("Você recebeu uma transferência de {$notification['amount']} de Suitpay Instituicao de Pagamentos Ltda.")
      ->icon('/nubank-apple-touch-icon.png')
      ->vibrate([200, 100, 200, 100, 200, 100, 200])
      ->tag('vibration-sample')
      ->options(['TTL' => 1000]);
  }

}

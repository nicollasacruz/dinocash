<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class PushDemo extends Notification
{

  use Queueable;

  public function via($notifiable)
  {
    return [WebPushChannel::class];
  }

  public function toWebPush($notifiable, $notification)
  {
    Log::info("Entrou no Push");
    return (new WebPushMessage)
      ->title("Transferência recebida")
      ->body("Você recebeu uma transferência de R$ 100,00 de Suitpay Instituicao de Pagamentos Ltda.")
      ->icon('../../public/nubank-apple-touch-icon.png')
      ->vibrate([200, 100, 200, 100, 200, 100, 200])
      ->tag('vibration-sample')
      ->options(['TTL' => 1000]);
  }

}

<?php

namespace App\Console\Commands;

use App\Events\PixReceived;
use App\Models\Deposit;
use App\Models\User;
use App\Notifications\PushDemo;
use App\Services\DepositService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckDepositsPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-deposits-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check deposits with status pending and check if paid in suitpay';

    /**
     * Execute the console command.
     */
    public function handle(DepositService $depositService)
    {
        $deposits = Deposit::where('type', 'pending')->get();
        $client = new Client();
        $i = 1;
        foreach ($deposits as $deposit) {
            $headers = [
                'ci' => env("SUITPAY_CI"),
                'cs' => env("SUITPAY_CS"),
                'Content-Type' => 'application/json',
            ];
            $body = [
                "typeTransaction" => "PIX",
                "idTransaction" => $deposit->externalId
            ];
            $request = new Request(
                'POST',
                'https://ws.suitpay.app/api/v1/gateway/consult-status-transaction',
                $headers,
                json_encode($body)
            );
            $res = $client->sendAsync($request)->wait();
            $status = json_decode($res->getBody(), true);
            if ($status === "PAID") {
                if ($depositService->aproveDeposit($deposit)) {
                    try {
                        event(new PixReceived($deposit->user));
                        foreach (User::where('role', 'admin')->get() as $admin) {
                            Notification::send($admin, new PushDemo('R$ ' . number_format(floatval($deposit->amount), 2, ',', '.')));
                        }
                        $this->info('Deposito aprovado   /  ' . $i);
                        $i++;
                    } catch (Exception $e) {
                        Log::error('Erro de notificar - ' . $e->getMessage());
                    }
                }
            } else {
                $this->info('Deposito não pago');
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckDepositsPaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-deposits-paid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): bool
    {
        try {

            $deposits = Deposit::where('type', 'pending')->get();
            $this->info('Checking ' . $deposits->count() . ' deposits');

            foreach ($deposits as $deposit) {
                if ($deposit->created_at->diffInDays(now()) > 1) {
                    $deposit->type = 'canceled';
                    $deposit->save();
                    $this->info('Deposit ' . $deposit->id . ' canceled');
                    continue;
                }

                $this->checkDeposit($deposit);
            }

            Deposit::where('type', 'canceled')
                ->whereDate('created_at', '<', now()->subDays(5))->delete();

            return true; // retorna sucesso

        } catch (Exception $e) {
            $this->error($e->getMessage());
            Log::error($e->getMessage());

            return false; // retorna falha
        }
    }

    private function checkDeposit(Deposit $deposit): void
    {
        try {


            $authValue = base64_encode(env('SECRETKEY_CASHTIME') . ':x');
            $endpoint = env('ENDPOINT_CASHTIME') . '/v1/transactions/' . $deposit->externalId;

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $authValue
            ])->get($endpoint);

            if ($response->json('status') != 400 && $response->json('type') === 'paid') {
                $deposit->status = 'paid';
                $deposit->save();
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
            Log::error($e->getMessage());
        }
    }
}

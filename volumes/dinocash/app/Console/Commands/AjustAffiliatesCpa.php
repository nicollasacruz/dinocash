<?php

namespace App\Console\Commands;

use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Models\User;
use App\Notifications\PushCPA;
use App\Services\AffiliateInvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Notification;

class AjustAffiliatesCpa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ajust-affiliates-cpa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $processedUserIds = [];

        $deposits = Deposit::where('type', 'paid')
            ->whereHas('user', function ($query) {
                $query->where('cpaCollected', null)
                    ->whereNotNull('affiliateId');
            })
            ->get();
        Log::info('Analisado total de ' . $deposits->count() . ' usuarios');

        $deposits->each(function ($deposit) use (&$processedUserIds) {
            $userId = $deposit->userId;

            // Verificar se o usuário já foi processado
            if (!in_array($userId, $processedUserIds)) {
                $processedUserIds[] = $userId;

                $affiliate = $deposit->user->affiliate;
                if ($affiliate) {
                    $this->createAffiliateHistory($deposit, $affiliate);
                    Log::info("CPA AJUSTADO");
                }
            }
        });
    }

    private function createAffiliateHistory(Deposit $deposit, User $affiliate)
    {
        $affiliateInvoiceService = new AffiliateInvoiceService();
        AffiliateHistory::create([
            'amount' => $affiliate->CPA,
            'affiliateId' => $affiliate->id,
            'affiliateInvoiceId' => ($affiliateInvoiceService->getInvoice($affiliate))->id,
            'userId' => $deposit->userId,
            'type' => 'CPA',
        ]);

        $deposit->user->update([
            'cpaCollected' => true,
            'cpaCollectedAt' => now(),
        ]);
        Log::info("CPA de {$affiliate->CPA} pago para o {$affiliate->email}");
        Notification::send($affiliate, new PushCPA('R$ ' . number_format(floatval($affiliate->CPA), 2, ',', '.')));
    }
}

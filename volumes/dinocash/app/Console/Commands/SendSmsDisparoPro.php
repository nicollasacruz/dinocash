<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendSmsDisparoPro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-sms-disparo-pro';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS DisparoPro';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        $token = "Bearer " . env('TOKEN_DISPARO_PRO');
        $msgArray = [];
        $chunkSize = 500;

        foreach ($users as $user) {
            $contact = $this->cleanAndFormatContact($user->contact);

            $msgArray[] = [
                'numero' => $contact,
                'servico' => 'short',
                'mensagem' => 'Jogo do Google dando Bônus de R$ 200 para quem depositar agora R$ 30 nesse link abaixo oficial! https://bit.ly/googlebonus200',
                'parceiro_id' => '5034e65a0c',
                'codificacao' => '0',
                'nome_campanha' => 'bonus200',
            ];

            // Verifica se atingiu o tamanho máximo antes de enviar a requisição
            if (count($msgArray) >= $chunkSize) {
                $this->sendRequest($msgArray, $token);
                $msgArray = []; // Limpa o array após o envio da requisição
            }
        }

        // Envia as mensagens restantes que não atingiram o tamanho máximo
        if (!empty($msgArray)) {
            $this->sendRequest($msgArray, $token);
        }
    }

    private function sendRequest($messages, $token)
    {
        $response = Http::withHeaders([
            'authorization' => $token,
            'content-type' => 'application/json',
        ])
            ->post('https://apihttp.disparopro.com.br:8433/mt', $messages);

        if ($response->failed()) {
            $this->info($response);
            echo "HTTP Error #:" . $response->status();
        } else {
            echo $response->body();
        }
    }

    private function cleanAndFormatContact($contact)
    {
        // Remove todos os caracteres não numéricos
        $cleanedContact = preg_replace('/[^0-9]/', '', $contact);

        // Adiciona o prefixo "55"
        return '55' . $cleanedContact;
    }
}

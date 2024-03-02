<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\BonusService;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $settings = Setting::first();

        $request->validate([
            'name' => 'required|string|min:8|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'contact' => 'required|string|max:14|unique:' . User::class,
            'document' => 'required|string|max:15|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact,
            'document' => $request->document,
            'password' => Hash::make($request->password),
            'CPA' => $settings->defaultCPA,
            'revShare' => $settings->defaultRevShare,
        ]);

        // $bonusService = new BonusService();
        // $bonusService->addFreeSpin($user, 2);

        event(new Registered($user));
        try {
            $token = "Bearer " . env('TOKEN_DISPARO_PRO');

            $contact = $this->cleanAndFormatContact($user->contact);

            $messages = [
                'numero' => $contact,
                'servico' => 'short',
                'mensagem' => 'Percebi que você criou uma conta no DINOCASH.IO, bora fazer esse depósito e ganhar 50% de bônus e +20 rodadas grátis!  https://dinocash.io/user/deposito',
                'parceiro_id' => '5034e65a0c',
                'codificacao' => '0',
                'nome_campanha' => 'cadastro',
            ];

            $response = Http::withHeaders([
                'authorization' => $token,
                'content-type' => 'application/json',
            ])
                ->post('https://apihttp.disparopro.com.br:8433/mt', $messages);

            if ($response->failed()) {
                Log::error('FALHA NO SMS  ------   ' . $response->body());
            } else {
                echo $response->body();
                Log::info('SMS  ------   ' . $response->body());
            }
        } catch (Exception $e) {
            Log::error('ERRO NO SMS  ------   ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    private function cleanAndFormatContact($contact)
    {
        // Remove todos os caracteres não numéricos
        $cleanedContact = preg_replace('/[^0-9]/', '', $contact);

        // Adiciona o prefixo "55"
        return '55' . $cleanedContact;
    }
}

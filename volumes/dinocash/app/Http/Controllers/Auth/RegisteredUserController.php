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
        ],[
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser uma string.',
            'name.min' => 'O nome deve ter pelo menos :min caracteres.',
            'name.max' => 'O nome não pode ter mais de :max caracteres.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.string' => 'O e-mail deve ser uma string.',
            'email.lowercase' => 'O e-mail deve estar em minúsculas.',
            'email.email' => 'Por favor, insira um endereço de e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais de :max caracteres.',
            'email.unique' => 'Este endereço de e-mail já está em uso.',
            'contact.required' => 'O campo de contato é obrigatório.',
            'contact.string' => 'O contato deve ser uma string.',
            'contact.max' => 'O contato não pode ter mais de :max caracteres.',
            'contact.unique' => 'Este contato já está em uso.',
            'document.required' => 'O campo documento é obrigatório.',
            'document.string' => 'O documento deve ser um texto.',
            'document.max' => 'O documento não pode ter mais de :max caracteres.',
            'document.unique' => 'Este documento já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'password.string' => 'A senha deve ser um texto.',
            'password.min' => 'A senha deve ter pelo menos :min caracteres.',
            'password.password' => 'A senha deve conter pelo menos uma letra maiúscula, uma letra minúscula e um número.',
            'password.max' => 'A senha não pode ter mais de :max caracteres.'
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

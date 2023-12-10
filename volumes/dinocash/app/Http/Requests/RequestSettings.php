<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestSettings extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'payout' => ['required', 'integer'], // Lucro
            'minWithdraw' => ['required', 'integer'], // Saque Minimo
            'maxWithdraw' => ['required', 'integer'], // Saque Maximo
            'minAmountPlay' => ['required', 'integer'], // Aposta Minima
            'maxAmountPlay' => ['required', 'integer'], // Aposta Maxima
            'minDeposit' => ['required', 'integer'], // Deposito Minimo
            'maxDeposit' => ['required', 'integer'], // Deposito Maximo
            'rollover' => ['required', 'integer'], // Rollover
            'defaultCPA' => ['required', 'integer'], // CPA Padrão
            'defaultRevShare' => ['required', 'integer'], // RevShare Padrão
            'autoPayWithdraw' => ['required', 'boolean'], // Saque Automatico
            'maxAutoPayWithdraw' => ['required', 'integer'], // Maximo Saque Automatico
        ];
    }
}

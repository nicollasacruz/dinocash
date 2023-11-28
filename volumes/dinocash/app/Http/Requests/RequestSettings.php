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
            'payout' => ['required', 'integer'],
            'minWithdraw' => ['required', 'integer'],
            'maxWithdraw' => ['required', 'integer'],
            'minDeposit' => ['required', 'integer'],
            'maxDeposit' => ['required', 'integer'],
            'rollover' => ['required', 'integer'],
            'defaultCPA' => ['required', 'integer'],
            'defaultRevShare' => ['required', 'integer'],
            'autoPayWithdraw' => ['required', 'boolean'],
            'maxAutoPayWithdraw' => ['required', 'integer'],
        ];
    }
}

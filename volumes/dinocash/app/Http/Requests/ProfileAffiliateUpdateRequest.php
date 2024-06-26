<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileAffiliateUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'invitation_link' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'wallet' => ['required', 'decimal:2', 'min:0', 'max:500000'],
            'isAffiliate' => ['required', 'bool'],
            'role' => ['required', 'string'],
            'CPA' => ['required', 'integer', 'min:0'],
            'revShare' => ['required', 'integer','max:100', 'min:0'],
        ];
    }
}

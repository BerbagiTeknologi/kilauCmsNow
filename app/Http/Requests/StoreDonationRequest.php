<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'type_donasi' => ['required', 'in:1,2'],
            'total' => ['required', 'numeric', 'min:1'],
            'id_program' => ['nullable', 'exists:programs,id'],
            'opsional_umum' => ['nullable', 'in:1,2'],
            'affiliate_sub' => ['nullable', 'string', 'max:64'],
            'referral_code' => ['nullable', 'string', 'max:64'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'feedback' => ['nullable', 'string', 'max:500'],
            'is_anonymous' => ['sometimes', 'boolean'],
        ];
    }
}

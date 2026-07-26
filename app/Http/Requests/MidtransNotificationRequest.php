<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MidtransNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'max:100', 'regex:/\Adonasi-[1-9][0-9]*\z/'],
            'status_code' => ['required', 'string', 'regex:/\A[0-9]{3}\z/'],
            'gross_amount' => ['required', 'string', 'max:30', 'regex:/\A[0-9]+(?:\.[0-9]{1,2})?\z/'],
            'signature_key' => ['required', 'string', 'size:128', 'regex:/\A[a-fA-F0-9]+\z/'],
            'transaction_status' => [
                'required',
                'string',
                Rule::in([
                    'authorize',
                    'capture',
                    'settlement',
                    'pending',
                    'deny',
                    'cancel',
                    'expire',
                    'failure',
                    'refund',
                    'partial_refund',
                    'chargeback',
                    'partial_chargeback',
                ]),
            ],
            'fraud_status' => ['nullable', 'string', Rule::in(['accept', 'challenge', 'deny'])],
        ];
    }
}

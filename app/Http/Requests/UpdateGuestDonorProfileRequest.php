<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateGuestDonorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['name', 'email', 'no_hp'] as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = trim((string) $this->input($field));
            $this->merge([$field => $value !== '' ? $value : null]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'no_hp' => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->hasAny(['name', 'email', 'no_hp'])) {
                $validator->errors()->add('profile', 'At least one profile field is required.');
            }
        });
    }
}

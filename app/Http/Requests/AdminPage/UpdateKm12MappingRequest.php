<?php

namespace App\Http\Requests\AdminPage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKm12MappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'km12_program_penerimaan_id' => ['nullable', 'integer', 'min:1'],
            'km12_sumber_dana_id' => ['nullable', 'integer', 'min:1'],
            'km12_program_name' => ['nullable', 'string', 'max:255'],
            'km12_sumber_dana_name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

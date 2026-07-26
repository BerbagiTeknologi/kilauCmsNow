<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ubah jadi true agar bisa dijalankan
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'icon_iklan' => 'nullable|string|max:50',
            'name_button_iklan' => 'nullable|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama donasi wajib diisi.',
            'file.image' => 'File harus berupa gambar.',
            'file.mimes' => 'Format gambar tidak valid (jpeg, png, jpg, gif, svg).',
        ];
    }
}

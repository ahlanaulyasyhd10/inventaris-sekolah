<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtendLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Batas atas diambil dari Pengaturan Sistem (tambahan hari per perpanjangan).
            'days' => ['required', 'integer', 'min:1', 'max:'.config('inventa.extension_days')],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'days.max' => 'Perpanjangan maksimal '.config('inventa.extension_days').' hari.',
        ];
    }
}

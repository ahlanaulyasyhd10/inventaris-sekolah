<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:1000'],
            'late_fine_per_day' => ['required', 'integer', 'min:0', 'max:10000000'],
            'default_loan_days' => ['required', 'integer', 'min:1', 'max:60'],
            'max_extensions' => ['required', 'integer', 'min:0', 'max:5'],
        ];
    }

    public function attributes(): array
    {
        return [
            'low_stock_threshold' => 'Batas stok menipis',
            'late_fine_per_day' => 'Denda per hari',
            'default_loan_days' => 'Lama pinjam bawaan',
            'max_extensions' => 'Maksimal perpanjangan',
        ];
    }
}

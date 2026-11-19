<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReturnLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'returned_at' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.$this->route('loan')->loan_date->toDateString()],
            'condition_on_return' => ['required', Rule::in(Item::CONDITIONS)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'pay_now' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'returned_at.before_or_equal' => 'Tanggal pengembalian tidak boleh melebihi hari ini.',
            'returned_at.after_or_equal' => 'Tanggal pengembalian tidak boleh sebelum tanggal pinjam.',
        ];
    }
}

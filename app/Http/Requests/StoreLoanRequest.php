<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Dipakai oleh User (mengajukan peminjaman) dan Admin (menambah peminjaman langsung).
 * Untuk Admin, field user_id wajib diisi.
 */
class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'item_id' => ['required', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
            'loan_date' => ['required', 'date', 'after_or_equal:today'],
            'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
        ];

        if ($this->user()?->isAdmin()) {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'loan_date.after_or_equal' => 'Tanggal pinjam tidak boleh sebelum hari ini.',
            'due_date.after_or_equal' => 'Batas pengembalian tidak boleh sebelum tanggal pinjam.',
        ];
    }

    /**
     * Validasi aturan bisnis: barang tidak rusak berat dan stok mencukupi.
     */
    public function after(): array
    {
        return [
            function ($validator) {
                if ($validator->errors()->has('item_id') || $validator->errors()->has('quantity')) {
                    return;
                }

                $item = Item::find($this->input('item_id'));

                if ($item->condition === 'rusak berat') {
                    $validator->errors()->add('item_id', 'Barang dalam kondisi rusak berat dan tidak dapat dipinjam.');
                } elseif ((int) $this->input('quantity') > $item->stock) {
                    $validator->errors()->add('quantity', 'Jumlah melebihi stok tersedia ('.format_number($item->stock).' unit).');
                }
            },
        ];
    }
}

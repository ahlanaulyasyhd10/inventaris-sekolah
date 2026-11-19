<?php

namespace App\Exports;

use App\Models\Loan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoansExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private Collection $loans)
    {
    }

    public function collection(): Collection
    {
        return $this->loans;
    }

    public function headings(): array
    {
        return ['No', 'Peminjam', 'Barang', 'Kode', 'Kategori', 'Jumlah', 'Tanggal Pinjam', 'Batas Kembali', 'Tanggal Kembali', 'Kondisi Kembali', 'Status'];
    }

    /** @param  Loan  $loan */
    public function map($loan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $loan->user->name,
            $loan->item->name,
            $loan->item->code,
            $loan->item->category->name ?? '-',
            $loan->quantity,
            format_date($loan->loan_date),
            format_date($loan->due_date),
            $loan->returnLog ? format_date($loan->returnLog->returned_at) : '-',
            $loan->returnLog->condition_on_return ?? '-',
            ucfirst($loan->status),
        ];
    }
}

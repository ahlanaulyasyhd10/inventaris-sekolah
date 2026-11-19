<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private int $no = 0;

    public function __construct(private Collection $items)
    {
    }

    public function collection(): Collection
    {
        return $this->items;
    }

    public function headings(): array
    {
        return ['No', 'Kode', 'Nama Barang', 'Kategori', 'Stok', 'Kondisi'];
    }

    public function map($item): array
    {
        $this->no++;

        return [$this->no, $item->code, $item->name, $item->category->name ?? '-', $item->stock, ucfirst($item->condition)];
    }
}

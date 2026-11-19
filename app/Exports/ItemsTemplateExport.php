<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/** Template impor barang: baris pertama adalah heading, baris kedua contoh pengisian. */
class ItemsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return ['kode', 'nama barang', 'kategori', 'stok', 'kondisi'];
    }

    public function array(): array
    {
        return [
            ['LAB-010', 'Mikroskop Digital', 'Alat Lab', 5, 'baik'],
            ['OLR-009', 'Bola Futsal', 'Alat Olahraga', 8, 'baik'],
        ];
    }
}

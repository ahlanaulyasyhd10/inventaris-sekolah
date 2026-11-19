<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Impor barang dari Excel/CSV. Kolom: kode, nama barang, kategori, stok, kondisi.
 * Barang dengan kode yang sudah ada akan diperbarui; kategori baru dibuat otomatis.
 */
class ItemsImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;

    public int $updated = 0;

    /** @var array<int, string> */
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2; // baris 1 = heading

            $code = trim((string) ($row['kode'] ?? ''));
            $name = trim((string) ($row['nama_barang'] ?? $row['nama'] ?? ''));
            $categoryName = trim((string) ($row['kategori'] ?? ''));
            $stock = $row['stok'] ?? null;
            $condition = strtolower(trim((string) ($row['kondisi'] ?? 'baik')));

            if ($code === '' && $name === '') {
                continue; // baris kosong
            }

            if ($code === '' || $name === '' || $categoryName === '') {
                $this->errors[] = "Baris {$line}: kode, nama barang, dan kategori wajib diisi.";
                continue;
            }

            if (! is_numeric($stock) || (int) $stock < 0) {
                $this->errors[] = "Baris {$line}: stok harus berupa angka 0 atau lebih.";
                continue;
            }

            if (! in_array($condition, Item::CONDITIONS, true)) {
                $this->errors[] = "Baris {$line}: kondisi harus baik, rusak ringan, atau rusak berat.";
                continue;
            }

            $category = Category::firstOrCreate(['name' => $categoryName]);
            $item = Item::withTrashed()->where('code', $code)->first();

            $data = ['category_id' => $category->id, 'name' => $name, 'stock' => (int) $stock, 'condition' => $condition];

            if ($item) {
                $item->restore();
                $item->update($data);
                $this->updated++;
            } else {
                Item::create($data + ['code' => $code]);
                $this->created++;
            }
        }
    }
}

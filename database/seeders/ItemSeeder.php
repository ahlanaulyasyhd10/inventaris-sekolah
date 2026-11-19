<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Stok di sini adalah stok total; LoanSeeder mengurangi stok untuk barang yang sedang dipinjam.
        foreach (InventoryData::items() as [$category, $name, $code, $stock, $condition]) {
            Item::updateOrCreate(
                ['code' => $code],
                [
                    'category_id' => Category::where('name', $category)->value('id'),
                    'name' => $name,
                    'stock' => $stock,
                    'condition' => $condition,
                ]
            );
        }
    }
}

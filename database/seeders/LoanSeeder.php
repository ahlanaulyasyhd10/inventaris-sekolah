<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        // Hindari data ganda jika seeder dijalankan lebih dari sekali.
        if (Loan::withTrashed()->exists()) {
            return;
        }

        foreach (InventoryData::loans() as $row) {
            $loanDate = now()->subDays($row['ago'])->startOfDay();
            $item = Item::where('code', $row['code'])->firstOrFail();

            (new Loan)->forceFill([
                'user_id' => User::where('email', $row['user'].'@sekolah.test')->value('id'),
                'item_id' => $item->id,
                'quantity' => $row['qty'],
                'loan_date' => $loanDate->toDateString(),
                'due_date' => $loanDate->copy()->addDays($row['dur'] + ($row['ext'] * config('inventa.extension_days')))->toDateString(),
                'status' => $row['status'],
                'extension_count' => $row['ext'],
                'created_at' => $loanDate->copy()->addHours(8),
                'updated_at' => $loanDate->copy()->addHours(8),
            ])->save();

            // Barang yang sedang dipinjam atau masih menunggu persetujuan (stok dipesan) mengurangi stok tersedia.
            if (in_array($row['status'], [...Loan::ACTIVE_STATUSES, Loan::STATUS_PENDING], true)) {
                $item->decrement('stock', $row['qty']);
            }
        }
    }
}

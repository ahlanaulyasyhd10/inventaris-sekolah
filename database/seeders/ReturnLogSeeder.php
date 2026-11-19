<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReturnLogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (InventoryData::loans() as $row) {
            if ($row['status'] !== Loan::STATUS_RETURNED) {
                continue;
            }

            // Cocokkan dengan peminjaman hasil LoanSeeder lewat peminjam, barang, dan tanggal pinjam
            // (bukan lewat urutan), supaya tetap benar walau tabel loans sudah berisi data lain.
            $loan = Loan::where('user_id', User::where('email', $row['user'].'@sekolah.test')->value('id'))
                ->where('item_id', Item::where('code', $row['code'])->value('id'))
                ->whereDate('loan_date', now()->subDays($row['ago'])->toDateString())
                ->where('status', Loan::STATUS_RETURNED)
                ->first();

            if (! $loan) {
                continue;
            }

            $lateDays = max(0, $row['ret'] - $row['dur']);

            ReturnLog::updateOrCreate(
                ['loan_id' => $loan->id],
                [
                    'returned_at' => $loan->loan_date->copy()->addDays($row['ret'])->toDateString(),
                    'condition_on_return' => $row['cond'],
                    'late_days' => $lateDays,
                    'fine_amount' => $lateDays * setting('late_fine_per_day'),
                    // Contoh data: denda Citra sudah lunas, denda Gilang belum dibayar.
                    'fine_paid_at' => ($lateDays > 0 && $row['user'] === 'citra') ? now()->subDays($row['ago'] - $row['ret'] - 1) : null,
                    'notes' => $row['note'],
                ]
            );
        }
    }
}

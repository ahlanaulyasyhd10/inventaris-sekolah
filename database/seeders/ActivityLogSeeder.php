<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        if (ActivityLog::exists()) {
            return;
        }

        $admin = User::where('role', 'admin')->value('id');
        $start = now()->subDays(145);
        $logs = [
            [$admin, 'login', 'Administrator masuk ke sistem.', $start->copy()],
            [$admin, 'create_category', 'Menambah '.count(InventoryData::categories()).' kategori barang inventaris.', $start->copy()->addMinutes(5)],
            [$admin, 'create_item', 'Menambah '.count(InventoryData::items()).' data barang inventaris.', $start->copy()->addMinutes(20)],
            [$admin, 'create_user', 'Menambah akun guru dan siswa.', $start->copy()->addMinutes(30)],
        ];

        foreach (Loan::with(['user', 'item', 'returnLog'])->orderBy('id')->get() as $loan) {
            $at = $loan->created_at->copy();
            $logs[] = [$loan->user_id, 'create_loan', "Mengajukan peminjaman {$loan->item->name} sebanyak {$loan->quantity} unit.", $at];

            if ($loan->status === Loan::STATUS_REJECTED) {
                $logs[] = [$admin, 'reject_loan', "Menolak peminjaman {$loan->item->name} oleh {$loan->user->name}.", $at->copy()->addHours(2)];
            } elseif ($loan->status !== Loan::STATUS_PENDING) {
                $logs[] = [$admin, 'approve_loan', "Menyetujui peminjaman {$loan->item->name} oleh {$loan->user->name}.", $at->copy()->addHours(1)];
            }

            if ($loan->extension_count > 0) {
                $logs[] = [$loan->user_id, 'extend_loan', "Memperpanjang peminjaman {$loan->item->name}.", $at->copy()->addDays(3)];
            }

            if ($loan->returnLog) {
                $logs[] = [$admin, 'return_loan', "Memverifikasi pengembalian {$loan->item->name} dari {$loan->user->name} (kondisi: {$loan->returnLog->condition_on_return}).", $loan->returnLog->returned_at->copy()->addHours(10)];
            }
        }

        usort($logs, fn ($a, $b) => $a[3] <=> $b[3]);

        foreach ($logs as [$user, $action, $description, $time]) {
            (new ActivityLog)->forceFill([
                'user_id' => $user,
                'action' => $action,
                'description' => $description,
                'created_at' => $time,
                'updated_at' => $time,
            ])->save();
        }

        (new ActivityLog)->forceFill(['user_id' => $admin, 'action' => 'login', 'description' => 'Administrator masuk ke sistem.', 'created_at' => now()->subHour(), 'updated_at' => now()->subHour()])->save();
    }
}

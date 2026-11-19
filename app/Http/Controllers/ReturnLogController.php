<?php

namespace App\Http\Controllers;

use App\Models\ReturnLog;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReturnLogController extends Controller
{
    public function index(): View
    {
        // Return log adalah catatan riwayat, sehingga hanya disediakan halaman lihat (tanpa hapus).
        return view('return-logs.index', [
            'returnLogs' => ReturnLog::with(['loan.user', 'loan.item'])->latest('returned_at')->latest('id')->get(),
        ]);
    }

    /** Admin menandai denda sudah dibayar (pembayaran dilakukan langsung ke Admin di sekolah). */
    public function payFine(ReturnLog $returnLog): RedirectResponse
    {
        if (! $returnLog->hasUnpaidFine()) {
            return back()->with('error', 'Tidak ada denda yang perlu ditandai lunas pada data ini.');
        }

        $returnLog->update(['fine_paid_at' => now()]);

        $returnLog->load(['loan.user', 'loan.item']);
        ActivityLogger::log('pay_fine', 'Menandai denda '.format_rupiah($returnLog->fine_amount)." dari {$returnLog->loan->user->name} ({$returnLog->loan->item->name}) sebagai lunas.");

        return back()->with('success', 'Denda ditandai lunas.');
    }
}

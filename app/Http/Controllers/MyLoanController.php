<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExtendLoanRequest;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Item;
use App\Models\Loan;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MyLoanController extends Controller
{
    public function index(Request $request): View
    {
        Loan::syncOverdue();

        return view('my-loans.index', [
            'loans' => $request->user()->loans()->with(['item', 'returnLog'])->latest('id')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('my-loans.create', [
            'items' => Item::with('category')->where('stock', '>', 0)->where('condition', '!=', 'rusak berat')->orderBy('name')->get(),
            'selectedItem' => $request->integer('item') ?: null,
        ]);
    }

    /** User mengajukan peminjaman: stok langsung dikurangi (dipesan), status menunggu persetujuan Admin. */
    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $loan = DB::transaction(function () use ($request) {
            $item = Item::lockForUpdate()->find($request->item_id);

            if (! $item || $item->stock < $request->quantity) {
                return null;
            }

            $item->decrement('stock', $request->quantity);

            return Loan::create([
                'user_id' => $request->user()->id,
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'loan_date' => $request->loan_date,
                'due_date' => $request->due_date,
                'status' => Loan::STATUS_PENDING,
            ]);
        });

        if (! $loan) {
            return back()->withInput()->with('error', 'Stok barang tidak mencukupi.');
        }

        $loan->load('item');

        ActivityLogger::log('create_loan', "Mengajukan peminjaman {$loan->item->name} sebanyak ".format_number($loan->quantity).' unit.');

        return redirect()->route('my-loans.index')->with('success', 'Pengajuan terkirim, stok barang sudah dipesan dan menunggu persetujuan Admin.');
    }

    public function destroy(Request $request, Loan $loan): RedirectResponse
    {
        abort_unless($loan->user_id === $request->user()->id, 403);

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Hanya pengajuan yang masih menunggu yang dapat dibatalkan.');
        }

        DB::transaction(function () use ($loan) {
            // Pembatalan mengembalikan stok yang tadinya dipesan.
            Item::withTrashed()->where('id', $loan->item_id)->increment('stock', $loan->quantity);
            $loan->delete();
        });

        ActivityLogger::log('delete_loan', "Membatalkan pengajuan peminjaman {$loan->item->name}.");

        return back()->with('success', 'Pengajuan peminjaman dibatalkan.');
    }

    /** Bukti peminjaman milik sendiri (hanya untuk pengajuan yang sudah disetujui). */
    public function receipt(Request $request, Loan $loan): Response|RedirectResponse
    {
        abort_unless($loan->user_id === $request->user()->id, 403);

        if (in_array($loan->status, [Loan::STATUS_PENDING, Loan::STATUS_REJECTED], true)) {
            return back()->with('error', 'Bukti peminjaman hanya tersedia untuk pengajuan yang sudah disetujui.');
        }

        $loan->load(['user', 'item.category', 'returnLog']);

        return Pdf::loadView('reports.loan-receipt', compact('loan'))->setPaper('a5', 'portrait')
            ->download('bukti-peminjaman-'.str_pad($loan->id, 5, '0', STR_PAD_LEFT).'.pdf');
    }

    /**
     * Perpanjang batas pengembalian. User memilih jumlah hari (1 sampai batas di Pengaturan Sistem)
     * lewat form; jumlah perpanjangan per peminjaman juga dibatasi oleh pengaturan.
     */
    public function extend(ExtendLoanRequest $request, Loan $loan): RedirectResponse
    {
        abort_unless($loan->user_id === $request->user()->id, 403);

        if (! $loan->canBeExtended()) {
            return back()->with('error', 'Peminjaman ini tidak dapat diperpanjang (batas perpanjangan tercapai, sudah terlambat, atau belum disetujui).');
        }

        $days = (int) $request->days;

        $loan->update([
            'due_date' => $loan->due_date->copy()->addDays($days),
            'extension_count' => $loan->extension_count + 1,
        ]);

        $loan->load('item');
        $reason = filled($request->reason) ? ' Alasan: '.$request->reason : '';
        ActivityLogger::log('extend_loan', "Memperpanjang peminjaman {$loan->item->name} selama {$days} hari hingga ".format_date($loan->due_date).'.'.$reason);

        return back()->with('success', 'Batas pengembalian diperpanjang '.$days.' hari, menjadi '.format_date($loan->due_date).'.');
    }
}

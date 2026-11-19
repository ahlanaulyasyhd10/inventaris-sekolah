<?php

namespace App\Http\Controllers;

use App\Exports\LoansExport;
use App\Http\Requests\LoanReportRequest;
use App\Http\Requests\ReturnLoanRequest;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use App\Support\ActivityLogger;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LoanController extends Controller
{
    public function index(): View
    {
        Loan::syncOverdue();

        return view('loans.index', [
            'loans' => Loan::with(['user', 'item'])->latest('id')->get(),
            'statuses' => Loan::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('loans.create', [
            'users' => User::where('role', User::ROLE_USER)->orderBy('name')->get(),
            'items' => Item::with('category')->where('stock', '>', 0)->where('condition', '!=', 'rusak berat')->orderBy('name')->get(),
        ]);
    }

    /** Admin mencatat peminjaman langsung (langsung berstatus "dipinjam" dan stok berkurang). */
    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $error = null;

        $loan = DB::transaction(function () use ($request, &$error) {
            $item = Item::lockForUpdate()->find($request->item_id);

            if ($item->stock < $request->quantity) {
                $error = 'Stok barang tidak mencukupi.';

                return null;
            }

            $item->decrement('stock', $request->quantity);

            return Loan::create([
                'user_id' => $request->user_id,
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'loan_date' => $request->loan_date,
                'due_date' => $request->due_date,
                'status' => Loan::STATUS_BORROWED,
            ]);
        });

        if (! $loan) {
            return back()->withInput()->with('error', $error);
        }

        $loan->load(['user', 'item']);
        ActivityLogger::log('create_loan', "Mencatat peminjaman {$loan->item->name} oleh {$loan->user->name} sebanyak ".format_number($loan->quantity).' unit.');

        return redirect()->route('admin.loans.index')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function show(Loan $loan): View
    {
        $loan->load(['user', 'item.category', 'returnLog']);

        return view('loans.show', compact('loan'));
    }

    /** Verifikasi: setujui pengajuan. Stok sudah dikurangi sejak User mengajukan, jadi tidak berubah lagi. */
    public function approve(Loan $loan): RedirectResponse
    {
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Hanya pengajuan berstatus menunggu yang dapat disetujui.');
        }

        $loan->update(['status' => Loan::STATUS_BORROWED]);

        $loan->load(['user', 'item']);
        ActivityLogger::log('approve_loan', "Menyetujui peminjaman {$loan->item->name} oleh {$loan->user->name}.");

        return back()->with('success', 'Pengajuan peminjaman disetujui.');
    }

    /** Tolak pengajuan: stok yang dipesan dikembalikan. */
    public function reject(Loan $loan): RedirectResponse
    {
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Hanya pengajuan berstatus menunggu yang dapat ditolak.');
        }

        DB::transaction(function () use ($loan) {
            Item::withTrashed()->where('id', $loan->item_id)->increment('stock', $loan->quantity);
            $loan->update(['status' => Loan::STATUS_REJECTED]);
        });

        $loan->load(['user', 'item']);
        ActivityLogger::log('reject_loan', "Menolak peminjaman {$loan->item->name} oleh {$loan->user->name}.");

        return back()->with('success', 'Pengajuan peminjaman ditolak dan stok dikembalikan.');
    }

    public function returnForm(Loan $loan): View|RedirectResponse
    {
        if (! $loan->isActive()) {
            return redirect()->route('admin.loans.index')->with('error', 'Peminjaman ini tidak sedang dipinjam.');
        }

        $loan->load(['user', 'item']);

        return view('loans.return', [
            'loan' => $loan,
            'conditions' => Item::CONDITIONS,
        ]);
    }

    /** Verifikasi pengembalian: buat return_log, status jadi "dikembalikan", stok bertambah. */
    public function returnStore(ReturnLoanRequest $request, Loan $loan): RedirectResponse
    {
        if (! $loan->isActive()) {
            return redirect()->route('admin.loans.index')->with('error', 'Peminjaman ini tidak sedang dipinjam.');
        }

        // Hitung keterlambatan dan denda otomatis berdasarkan pengaturan sistem.
        $lateDays = max(0, (int) $loan->due_date->copy()->startOfDay()->diffInDays(Carbon::parse($request->returned_at)->startOfDay(), false));
        $fine = $lateDays * setting('late_fine_per_day');
        // Jika denda dibayar langsung saat pengembalian, sekaligus ditandai lunas.
        $paidNow = $fine > 0 && $request->boolean('pay_now');

        DB::transaction(function () use ($request, $loan, $lateDays, $fine, $paidNow) {
            ReturnLog::create([
                'loan_id' => $loan->id,
                'returned_at' => $request->returned_at,
                'condition_on_return' => $request->condition_on_return,
                'late_days' => $lateDays,
                'fine_amount' => $fine,
                'fine_paid_at' => $paidNow ? now() : null,
                'notes' => $request->notes,
            ]);

            $loan->update(['status' => Loan::STATUS_RETURNED]);

            Item::withTrashed()->where('id', $loan->item_id)->increment('stock', $loan->quantity);
        });

        $loan->load(['user', 'item']);
        ActivityLogger::log('return_loan', "Memverifikasi pengembalian {$loan->item->name} dari {$loan->user->name} (kondisi: {$request->condition_on_return}, denda: ".format_rupiah($fine).($paidNow ? ', lunas' : '').').');

        return redirect()->route('admin.loans.index')->with('success', 'Pengembalian berhasil dicatat dan stok barang diperbarui.'.($paidNow ? ' Denda '.format_rupiah($fine).' ditandai lunas.' : ($fine > 0 ? ' Denda '.format_rupiah($fine).' belum dibayar.' : '')));
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        if ($loan->isActive()) {
            return back()->with('error', 'Peminjaman yang sedang berjalan tidak dapat dihapus. Selesaikan pengembalian terlebih dahulu.');
        }

        $loan->load(['user', 'item']);

        DB::transaction(function () use ($loan) {
            // Pengajuan yang masih menunggu menahan stok, jadi stoknya dikembalikan saat dihapus.
            if ($loan->status === Loan::STATUS_PENDING) {
                Item::withTrashed()->where('id', $loan->item_id)->increment('stock', $loan->quantity);
            }

            $loan->delete(); // Soft Delete
        });

        ActivityLogger::log('delete_loan', "Menghapus data peminjaman {$loan->item->name} oleh {$loan->user->name}.");

        return redirect()->route('admin.loans.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    /** Bukti peminjaman (PDF). */
    public function receipt(Loan $loan): Response
    {
        $loan->load(['user', 'item.category', 'returnLog']);

        return Pdf::loadView('reports.loan-receipt', compact('loan'))->setPaper('a5', 'portrait')
            ->download('bukti-peminjaman-'.str_pad($loan->id, 5, '0', STR_PAD_LEFT).'.pdf');
    }

    public function exportPdf(LoanReportRequest $request): Response
    {
        $loans = $this->reportQuery($request)->get();

        ActivityLogger::log('export_loans', 'Mengekspor laporan peminjaman ke PDF.');

        return Pdf::loadView('reports.loans', [
            'loans' => $loans,
            'from' => $request->from,
            'to' => $request->to,
            'status' => $request->status,
        ])->setPaper('a4', 'landscape')->download('laporan-peminjaman-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(LoanReportRequest $request): BinaryFileResponse
    {
        $loans = $this->reportQuery($request)->get();

        ActivityLogger::log('export_loans', 'Mengekspor laporan peminjaman ke Excel.');

        return Excel::download(new LoansExport($loans), 'laporan-peminjaman-'.now()->format('Ymd-His').'.xlsx');
    }

    private function reportQuery(LoanReportRequest $request)
    {
        Loan::syncOverdue();

        return Loan::with(['user', 'item.category', 'returnLog'])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('loan_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('loan_date', '<=', $request->to))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('loan_date')
            ->orderBy('id');
    }
}

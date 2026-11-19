<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Pastikan status "terlambat" selalu mutakhir saat dashboard dibuka.
        Loan::syncOverdue();

        $user = $request->user();

        return $user->isAdmin() ? $this->admin() : $this->user($user);
    }

    private function admin(): View
    {
        $valid = [Loan::STATUS_BORROWED, Loan::STATUS_RETURNED, Loan::STATUS_LATE];

        $totalItems = Item::count();
        $totalStock = (int) Item::sum('stock');
        $borrowed = Loan::where('status', Loan::STATUS_BORROWED)->count();
        $late = Loan::where('status', Loan::STATUS_LATE)->count();
        $returned = Loan::where('status', Loan::STATUS_RETURNED)->count();
        $borrowedQty = (int) Loan::whereIn('status', [...Loan::ACTIVE_STATUSES, Loan::STATUS_PENDING])->sum('quantity');

        $topItems = Item::withCount(['loans as loans_count' => fn ($q) => $q->whereIn('status', $valid)])
            ->orderByDesc('loans_count')
            ->take(5)
            ->get()
            ->filter(fn ($item) => $item->loans_count > 0);

        $topUsers = User::where('role', User::ROLE_USER)
            ->withCount(['loans as loans_count' => fn ($q) => $q->whereIn('status', $valid)])
            ->orderByDesc('loans_count')
            ->take(5)
            ->get()
            ->filter(fn ($u) => $u->loans_count > 0);

        // Tren peminjaman 6 bulan terakhir untuk grafik.
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $chartLabels[] = $month->locale('id')->translatedFormat('M Y');
            $chartData[] = Loan::whereYear('loan_date', $month->year)
                ->whereMonth('loan_date', $month->month)
                ->whereIn('status', $valid)
                ->count();
        }

        $categories = Category::withCount('items')->orderBy('name')->get();

        return view('dashboard.admin', [
            'totalItems' => $totalItems,
            'totalStock' => $totalStock,
            'borrowed' => $borrowed,
            'late' => $late,
            'returned' => $returned,
            'totalFine' => (int) ReturnLog::sum('fine_amount'),
            'unpaidFine' => (int) ReturnLog::whereNull('fine_paid_at')->sum('fine_amount'),
            'runningFine' => (int) Loan::where('status', Loan::STATUS_LATE)->get()->sum(fn ($loan) => $loan->accruedFine()),
            'availabilityPct' => ($totalStock + $borrowedQty) > 0 ? (int) round($totalStock / ($totalStock + $borrowedQty) * 100) : 0,
            'goodConditionPct' => $totalItems > 0 ? (int) round(Item::where('condition', 'baik')->count() / $totalItems * 100) : 0,
            'returnRate' => ($returned + $borrowed + $late) > 0 ? (int) round($returned / ($returned + $borrowed + $late) * 100) : 0,
            'categoryCount' => $categories->count(),
            'userCount' => User::where('role', User::ROLE_USER)->count(),
            'pendingLoans' => Loan::with(['user', 'item'])->where('status', Loan::STATUS_PENDING)->latest()->take(5)->get(),
            'pendingCount' => Loan::where('status', Loan::STATUS_PENDING)->count(),
            'lateLoans' => Loan::with(['user', 'item'])->where('status', Loan::STATUS_LATE)->orderBy('due_date')->take(5)->get(),
            'lowStockItems' => Item::with('category')->lowStock()->orderBy('stock')->orderBy('name')->take(6)->get(),
            'lowStockCount' => Item::lowStock()->count(),
            'topItems' => $topItems,
            'topUsers' => $topUsers,
            'recentActivities' => ActivityLog::with('user')->latest('id')->take(8)->get(),
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'categoryLabels' => $categories->pluck('name'),
            'categoryData' => $categories->pluck('items_count'),
        ]);
    }

    private function user(User $user): View
    {
        $loans = $user->loans();

        return view('dashboard.user', [
            'active' => (clone $loans)->whereIn('status', Loan::ACTIVE_STATUSES)->count(),
            'pending' => (clone $loans)->where('status', Loan::STATUS_PENDING)->count(),
            'late' => (clone $loans)->where('status', Loan::STATUS_LATE)->count(),
            'total' => (clone $loans)->count(),
            'returned' => (clone $loans)->where('status', Loan::STATUS_RETURNED)->count(),
            'activeLoans' => $user->loans()->with('item')->whereIn('status', Loan::ACTIVE_STATUSES)->orderBy('due_date')->take(5)->get(),
            'recentLoans' => $user->loans()->with('item')->latest()->take(6)->get(),
        ]);
    }
}

<?php

namespace App\Providers;

use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Events\Registered;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('id');
        Paginator::useBootstrapFive();

        // Email reset kata sandi dalam Bahasa Indonesia.
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

            return (new MailMessage)
                ->subject('Atur ulang kata sandi Inventa')
                ->greeting('Halo, '.$notifiable->name.'!')
                ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Inventa kamu.')
                ->action('Atur Ulang Kata Sandi', $url)
                ->line('Tautan ini berlaku selama '.$minutes.' menit.')
                ->line('Jika kamu tidak merasa memintanya, abaikan saja email ini.')
                ->salutation('Salam, Inventa - Sistem Inventaris Sekolah');
        });

        // Fitur notifikasi: daftar peringatan di ikon lonceng navbar.
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $notifications = [];

            if ($user && $user->isAdmin()) {
                $pending = Loan::where('status', Loan::STATUS_PENDING)->count();
                $late = Loan::whereIn('status', [Loan::STATUS_LATE])->count();
                $low = Item::lowStock()->count();

                $unpaid = ReturnLog::whereNull('fine_paid_at')->where('fine_amount', '>', 0)->count();

                if ($pending) {
                    $notifications[] = ['icon' => 'ti-hourglass', 'color' => 'bg-azure', 'title' => format_number($pending).' pengajuan menunggu persetujuan', 'desc' => 'Verifikasi peminjaman', 'url' => route('admin.loans.index')];
                }
                if ($late) {
                    $notifications[] = ['icon' => 'ti-clock-exclamation', 'color' => 'bg-red', 'title' => format_number($late).' peminjaman terlambat', 'desc' => 'Tindak lanjuti pengembalian barang', 'url' => route('admin.loans.index')];
                }
                if ($unpaid) {
                    $notifications[] = ['icon' => 'ti-cash', 'color' => 'bg-purple', 'title' => format_number($unpaid).' denda belum dibayar', 'desc' => 'Tandai lunas setelah pembayaran diterima', 'url' => route('admin.return-logs.index')];
                }
                if ($low) {
                    $notifications[] = ['icon' => 'ti-alert-triangle', 'color' => 'bg-yellow', 'title' => format_number($low).' barang stok menipis', 'desc' => 'Stok '.setting('low_stock_threshold').' unit atau kurang', 'url' => route('admin.items.index')];
                }
            } elseif ($user) {
                $dueSoon = $user->loans()->with('item')->where('status', Loan::STATUS_BORROWED)
                    ->whereBetween('due_date', [today(), today()->addDays(2)])->orderBy('due_date')->get();
                $late = $user->loans()->where('status', Loan::STATUS_LATE)->count();
                $lateFine = $user->loans()->where('status', Loan::STATUS_LATE)->get()->sum(fn ($l) => $l->accruedFine());

                $unpaidFine = (int) ReturnLog::whereHas('loan', fn ($q) => $q->where('user_id', $user->id))
                    ->whereNull('fine_paid_at')->sum('fine_amount');

                if ($unpaidFine > 0) {
                    $notifications[] = ['icon' => 'ti-cash', 'color' => 'bg-purple', 'title' => 'Denda belum dibayar '.format_rupiah($unpaidFine), 'desc' => 'Bayar langsung ke Admin sekolah', 'url' => route('my-loans.index')];
                }
                foreach ($dueSoon->take(3) as $loan) {
                    $days = (int) today()->diffInDays($loan->due_date, false);
                    $when = $days <= 0 ? 'hari ini' : ($days === 1 ? 'besok' : $days.' hari lagi');
                    $desc = 'Kembalikan ke Admin sebelum '.format_date($loan->due_date).($loan->canBeExtended() ? ' atau perpanjang' : '');

                    $notifications[] = ['icon' => 'ti-calendar-due', 'color' => 'bg-yellow', 'title' => $loan->item->name.' jatuh tempo '.$when, 'desc' => $desc, 'url' => route('my-loans.index')];
                }
                if ($late) {
                    $notifications[] = ['icon' => 'ti-clock-exclamation', 'color' => 'bg-red', 'title' => format_number($late).' peminjaman terlambat', 'desc' => $lateFine > 0 ? 'Denda berjalan '.format_rupiah($lateFine).', segera kembalikan ke Admin' : 'Segera kembalikan barang ke Admin', 'url' => route('my-loans.index')];
                }
            }

            $view->with('notifications', $notifications);
        });

        // Activity log untuk aktivitas autentikasi (login, logout, register).
        Event::listen(Login::class, function (Login $event) {
            ActivityLogger::log('login', "{$event->user->name} masuk ke sistem.", $event->user->getAuthIdentifier());
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                ActivityLogger::log('logout', "{$event->user->name} keluar dari sistem.", $event->user->getAuthIdentifier());
            }
        });

        Event::listen(PasswordReset::class, function (PasswordReset $event) {
            ActivityLogger::log('reset_password', "{$event->user->name} mengatur ulang kata sandi lewat email.", $event->user->getAuthIdentifier());
        });

        Event::listen(Registered::class, function (Registered $event) {
            ActivityLogger::log('register', "{$event->user->name} mendaftar akun baru.", $event->user->getAuthIdentifier());
        });
    }
}

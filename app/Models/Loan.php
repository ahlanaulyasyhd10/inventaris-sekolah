<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'menunggu';
    public const STATUS_BORROWED = 'dipinjam';
    public const STATUS_RETURNED = 'dikembalikan';
    public const STATUS_LATE = 'terlambat';
    public const STATUS_REJECTED = 'ditolak';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_BORROWED,
        self::STATUS_RETURNED,
        self::STATUS_LATE,
        self::STATUS_REJECTED,
    ];

    /** Status yang artinya barang sedang berada di tangan peminjam. */
    public const ACTIVE_STATUSES = [self::STATUS_BORROWED, self::STATUS_LATE];

    protected $fillable = [
        'user_id', 'item_id', 'quantity', 'loan_date', 'due_date', 'status', 'extension_count',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'extension_count' => 'integer',
            'loan_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function returnLog(): HasOne
    {
        return $this->hasOne(ReturnLog::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }

    /** Hanya peminjaman yang sedang dipinjam (belum terlambat) dan belum pernah diperpanjang. */
    public function canBeExtended(): bool
    {
        return $this->status === self::STATUS_BORROWED
            && $this->extension_count < setting('max_extensions')
            && $this->due_date->gte(today());
    }

    /** Jumlah hari terlambat sampai hari ini (hanya untuk peminjaman yang belum dikembalikan). */
    public function daysLate(): int
    {
        if (! $this->isActive() || $this->due_date->gte(today())) {
            return 0;
        }

        return (int) $this->due_date->copy()->startOfDay()->diffInDays(today());
    }

    /** Denda yang sudah berjalan sampai hari ini (akan dikunci saat pengembalian diverifikasi). */
    public function accruedFine(): int
    {
        return $this->daysLate() * setting('late_fine_per_day');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-azure-lt',
            self::STATUS_BORROWED => 'bg-blue-lt',
            self::STATUS_RETURNED => 'bg-green-lt',
            self::STATUS_LATE => 'bg-red-lt',
            default => 'bg-secondary-lt',
        };
    }

    /**
     * Ubah status "dipinjam" menjadi "terlambat" jika sudah melewati due_date.
     * Dipanggil dari dashboard/daftar peminjaman dan dari scheduler harian.
     */
    public static function syncOverdue(): int
    {
        return static::where('status', self::STATUS_BORROWED)
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => self::STATUS_LATE]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnLog extends Model
{
    use SoftDeletes;

    protected $fillable = ['loan_id', 'returned_at', 'condition_on_return', 'late_days', 'fine_amount', 'fine_paid_at', 'notes'];

    protected function casts(): array
    {
        return ['returned_at' => 'date', 'fine_paid_at' => 'datetime'];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class)->withTrashed();
    }

    public function hasUnpaidFine(): bool
    {
        return $this->fine_amount > 0 && $this->fine_paid_at === null;
    }

    /** Jumlah hari keterlambatan (0 jika tepat waktu), dicatat saat pengembalian. */
    public function lateDays(): int
    {
        return (int) $this->late_days;
    }
}

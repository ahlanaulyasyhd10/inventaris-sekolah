<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    public const CONDITIONS = ['baik', 'rusak ringan', 'rusak berat'];

    protected $fillable = ['category_id', 'name', 'code', 'stock', 'condition', 'photo'];

    protected function casts(): array
    {
        return ['stock' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /** Barang yang masih bisa dipinjam: stok tersedia dan tidak rusak berat. */
    /**
     * URL gambar barang: foto yang diunggah Admin (butuh `php artisan storage:link`),
     * atau gambar bawaan berdasarkan kode barang, atau null jika keduanya tidak ada.
     */
    public function photoUrl(): ?string
    {
        if ($this->photo) {
            return asset('storage/'.$this->photo);
        }

        $default = 'images/items/'.$this->code.'.png';

        return file_exists(public_path($default)) ? asset($default) : null;
    }

    /** True jika gambar yang tampil adalah gambar bawaan (bukan unggahan Admin). */
    public function usesDefaultPhoto(): bool
    {
        return ! $this->photo && $this->photoUrl() !== null;
    }

    /** Stok menipis: sama atau di bawah batas pengaturan, dan barang tidak rusak berat. Dipakai di semua tampilan agar jumlahnya konsisten. */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('stock', '<=', setting('low_stock_threshold'))->where('condition', '!=', 'rusak berat');
    }

    public function isLowStock(): bool
    {
        return $this->stock <= setting('low_stock_threshold') && $this->condition !== 'rusak berat';
    }

    public function isBorrowable(): bool
    {
        return $this->stock > 0 && $this->condition !== 'rusak berat';
    }

    public function conditionBadge(): string
    {
        return match ($this->condition) {
            'baik' => 'bg-green-lt',
            'rusak ringan' => 'bg-yellow-lt',
            default => 'bg-red-lt',
        };
    }
}

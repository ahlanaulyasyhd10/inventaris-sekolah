<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Cache per-request agar tidak query berulang. */
    private static ?array $cache = null;

    public static function all_values(): array
    {
        if (self::$cache === null) {
            // Aman dipanggil sebelum migrate (mis. saat php artisan migrate pertama kali).
            self::$cache = Schema::hasTable('settings') ? static::pluck('value', 'key')->all() : [];
        }

        return self::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all_values()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        self::$cache = null;
    }
}

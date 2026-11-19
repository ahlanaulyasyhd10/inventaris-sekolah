<?php

use Carbon\Carbon;

if (! function_exists('format_number')) {
    /**
     * Format angka dengan pemisah ribuan format Indonesia (contoh: 1.250).
     */
    function format_number(int|float|string|null $value, int $decimals = 0): string
    {
        return number_format((float) $value, $decimals, ',', '.');
    }
}

if (! function_exists('format_date')) {
    /**
     * Format tanggal Indonesia (contoh: 1 Feb 2026).
     */
    function format_date(mixed $date): string
    {
        if (blank($date)) {
            return '-';
        }

        return Carbon::parse($date)->locale('id')->translatedFormat('j M Y');
    }
}

if (! function_exists('format_datetime')) {
    /**
     * Format tanggal dan jam Indonesia (contoh: 1 Feb 2026 14:30).
     */
    function format_datetime(mixed $date): string
    {
        if (blank($date)) {
            return '-';
        }

        return Carbon::parse($date)->locale('id')->translatedFormat('j M Y H:i');
    }
}

if (! function_exists('setting')) {
    /**
     * Ambil pengaturan sistem: nilai dari tabel settings, jika kosong pakai config/inventa.php.
     * Nilai angka dikembalikan sebagai integer.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        $value = \App\Models\Setting::get($key, config('inventa.'.$key, $default));

        return is_numeric($value) ? (int) $value : $value;
    }
}

if (! function_exists('format_rupiah')) {
    /**
     * Format mata uang Rupiah (contoh: Rp 15.000).
     */
    function format_rupiah(int|float|string|null $value): string
    {
        return 'Rp '.number_format((float) $value, 0, ',', '.');
    }
}

if (! function_exists('whatsapp_url')) {
    /**
     * Ubah nomor telepon (08xx / +62xx) menjadi tautan WhatsApp. Null jika nomor kosong.
     */
    function whatsapp_url(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits;
    }
}

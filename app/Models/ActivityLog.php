<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'description'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Label berbahasa Indonesia untuk kode aksi (kode di database tetap bahasa Inggris, sesuai contoh brief). */
    public const ACTION_LABELS = [
        'login' => 'Masuk',
        'logout' => 'Keluar',
        'register' => 'Registrasi',
        'create_category' => 'Tambah kategori',
        'update_category' => 'Ubah kategori',
        'delete_category' => 'Hapus kategori',
        'create_item' => 'Tambah barang',
        'update_item' => 'Ubah barang',
        'delete_item' => 'Hapus barang',
        'import_items' => 'Impor barang',
        'export_items' => 'Ekspor barang',
        'create_loan' => 'Pengajuan peminjaman',
        'approve_loan' => 'Persetujuan peminjaman',
        'reject_loan' => 'Penolakan peminjaman',
        'extend_loan' => 'Perpanjangan peminjaman',
        'return_loan' => 'Pengembalian barang',
        'delete_loan' => 'Hapus peminjaman',
        'export_loans' => 'Ekspor peminjaman',
        'pay_fine' => 'Pelunasan denda',
        'create_user' => 'Tambah user',
        'update_user' => 'Ubah user',
        'delete_user' => 'Hapus user',
        'update_profile' => 'Ubah profil',
        'update_settings' => 'Ubah pengaturan',
    ];

    public static function labelFor(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }

    public function actionLabel(): string
    {
        return self::labelFor($this->action);
    }

    public function actionBadge(): string
    {
        return match (true) {
            str_starts_with($this->action, 'create'), $this->action === 'register' => 'bg-green-lt',
            str_starts_with($this->action, 'update'), str_starts_with($this->action, 'approve'), $this->action === 'extend_loan' => 'bg-blue-lt',
            $this->action === 'pay_fine' => 'bg-purple-lt',
            str_starts_with($this->action, 'delete'), str_starts_with($this->action, 'reject') => 'bg-red-lt',
            in_array($this->action, ['login', 'logout'], true) => 'bg-purple-lt',
            default => 'bg-secondary-lt',
        };
    }
}

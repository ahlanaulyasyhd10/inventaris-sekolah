<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Catat satu baris audit trail ke tabel activity_logs.
     * Jika $userId tidak diberikan, dipakai user yang sedang login.
     */
    public static function log(string $action, string $description, ?int $userId = null): void
    {
        ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'description' => $description,
        ]);
    }
}

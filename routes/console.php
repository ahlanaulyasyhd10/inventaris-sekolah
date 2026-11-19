<?php

use Illuminate\Support\Facades\Schedule;

// Opsional: menandai peminjaman terlambat setiap hari.
// Status "terlambat" sebenarnya juga otomatis diperbarui setiap kali halaman terkait dibuka.
// Jalankan scheduler dengan: php artisan schedule:work (atau cron: php artisan schedule:run)
Schedule::command('loans:mark-overdue')->daily();

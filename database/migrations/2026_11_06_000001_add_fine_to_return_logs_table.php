<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Denda keterlambatan dicatat saat pengembalian diverifikasi.
        Schema::table('return_logs', function (Blueprint $table) {
            $table->unsignedInteger('late_days')->default(0)->after('condition_on_return');
            $table->unsignedInteger('fine_amount')->default(0)->after('late_days');
        });
    }

    public function down(): void
    {
        Schema::table('return_logs', function (Blueprint $table) {
            $table->dropColumn(['late_days', 'fine_amount']);
        });
    }
};

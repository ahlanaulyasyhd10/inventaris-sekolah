<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fitur tambahan: perpanjangan peminjaman (maksimal 1 kali per peminjaman).
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedTinyInteger('extension_count')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('extension_count');
        });
    }
};

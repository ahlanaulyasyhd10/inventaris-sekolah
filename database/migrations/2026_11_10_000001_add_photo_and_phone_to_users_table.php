<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foto profil dan nomor WhatsApp (opsional) agar Admin bisa menghubungi peminjam.
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('role');
            $table->string('phone', 20)->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'phone']);
        });
    }
};

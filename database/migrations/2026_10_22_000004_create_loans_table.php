<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity');
            $table->date('loan_date');
            $table->date('due_date');
            // dipinjam, dikembalikan, terlambat = status wajib sesuai brief.
            // menunggu & ditolak = tambahan untuk alur persetujuan (pengembangan lanjutan).
            $table->enum('status', ['menunggu', 'dipinjam', 'dikembalikan', 'terlambat', 'ditolak'])
                ->default('menunggu');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

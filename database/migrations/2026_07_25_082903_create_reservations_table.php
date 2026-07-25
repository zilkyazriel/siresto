<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            // Meja opsional; jika meja dihapus, reservasi tetap ada (meja jadi null).
            $table->foreignId('dining_table_id')->nullable()->constrained('dining_tables')->nullOnDelete();
            $table->dateTime('reserved_at');                 // waktu reservasi
            $table->unsignedInteger('party_size')->default(1); // jumlah orang
            $table->string('status')->default('menunggu');   // menunggu | hadir | selesai | batal
            $table->text('note')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // staf pencatat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
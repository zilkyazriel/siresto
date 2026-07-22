<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2); // jumlah bahan dipakai per 1 porsi (satuan mengikuti stok)
            $table->timestamps();

            $table->unique(['menu_id', 'stock_id']); // 1 menu tidak boleh punya bahan yang sama 2x
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_ingredients');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('complaints', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->string('customer_name')->nullable();
        $table->text('content');
        $table->string('status')->default('baru'); // baru | diproses | selesai
        $table->text('resolution')->nullable();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

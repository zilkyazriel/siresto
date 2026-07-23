<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        // Kembalikan kolom persis seperti aslinya bila migration di-rollback.
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('status')->default('antri')->after('subtotal');
        });
    }
};
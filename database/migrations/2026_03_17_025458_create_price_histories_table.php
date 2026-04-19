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
        Schema::create('price_histories', function (Blueprint $table) {
    $table->id('ma_lich_su');

    $table->unsignedBigInteger('ma_san_pham');
    $table->unsignedBigInteger('ma_nha_cung_cap');

    $table->decimal('gia_nhap', 10, 2);
    $table->decimal('gia_thi_truong', 10, 2);
    $table->decimal('gia_ban', 10, 2); // ⚠️ QUAN TRỌNG

    $table->timestamp('thoi_gian_cap_nhat');
    $table->timestamps(); 
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};

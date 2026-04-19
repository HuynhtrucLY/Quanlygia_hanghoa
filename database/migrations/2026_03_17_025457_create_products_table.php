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
        Schema::create('products', function (Blueprint $table) {
            $table->id('ma_san_pham');
            $table->string('ten_san_pham');
            $table->unsignedBigInteger('ma_danh_muc');
            $table->string('don_vi_tinh');
            $table->string('xuat_xu');

            $table->foreign('ma_danh_muc')
                ->references('ma_danh_muc')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

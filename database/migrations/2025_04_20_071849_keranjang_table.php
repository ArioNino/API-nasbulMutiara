<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id('keranjang_id');
            $table->foreignId('id_transaksi')
                ->nullable()
                ->constrained('transaksi', 'transaksi_id')
                ->noActionOnUpdate()
                ->noActionOnDelete();
            $table->foreignId('id_produk')
                ->constrained('produk', 'produk_id')
                ->noActionOnUpdate()
                ->noActionOnDelete();
            $table->foreignId('id_user')
                ->constrained('users', 'user_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('quantity');
            $table->boolean('israted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};

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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('transaksi_id');
            $table->integer('total');
            $table->string('status');
            $table->string('resi')->nullable();
            $table->foreignId('id_alamat')
                ->constrained('alamat', 'alamat_id')
                ->noActionOnUpdate()
                ->noActionOnDelete();
            $table->longText('snaptoken');
            $table->timestamp('tanggal_transaksi')->useCurrent();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }

};

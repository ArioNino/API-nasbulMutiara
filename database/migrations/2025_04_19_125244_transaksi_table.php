<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('transaksi_id');
            $table->integer('total');
            $table->string('status');
            $table->string('jenis_pembayaran');
            $table->foreignId('id_alamat')
                ->nullable()
                ->constrained('alamat', 'alamat_id')
                ->nullOnDelete();
            $table->longText('snaptoken');
            $table->timestamps();
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

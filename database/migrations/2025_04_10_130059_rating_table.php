<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('rating', function (Blueprint $table) {
    //         $table->id('rating_id');
    //         $table->foreignId('id_transaksi_item')
    //             ->constrained('transaksi_item', 'transaksi_item_id')
    //             ->cascadeOnUpdate()
    //             ->cascadeOnDelete();
    //         $table->integer('rating');
    //         $table->string('comment')->nullable();
    //         $table->longText('gambar')->nullable();
    //         $table->timestamps();
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

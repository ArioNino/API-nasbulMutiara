<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alamat', function (Blueprint $table) {
            $table->id('alamat_id');
            $table->foreignId('id_user')
                ->constrained('users', 'user_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('no_telepon');
            $table->string('label_alamat');
            $table->string('nama_penerima');
            $table->string('detail');
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('provinsi');
            $table->integer('isPrimary');
            $table->string('catatan_kurir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        //
    }
};

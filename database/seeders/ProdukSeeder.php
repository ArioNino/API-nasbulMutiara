<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Produk::create([
            "nama_produk"=> "Nasi Kebuli sapi",
            "kategori"=> "SAPI",
            "deskripsi"=> "Nasi kebuli dengan topping daging sapi",
            "ukuran"=> "L",
            "harga"=> 55000,
            "gambar"=> null,
            "stok"=> 50
        ]);
    }
}

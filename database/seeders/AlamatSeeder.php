<?php

namespace Database\Seeders;

use App\Models\Alamat;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AlamatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Alamat::create([
                "label_alamat" => "Rumah",
                "nama_penerima" => "Ario Elnino",
                "no_telepon" => "081234567890",
                "detail" => "Jl. Kumbang No. 14",
                "kelurahan" => "BABAKAN",
                "kecamatan" => "BOGOR TENGAH",
                "kabupaten" => "KOTA BOGOR",
                "provinsi" => "JAWA BARAT",
                "latitude" => -6.585863266220824,
                "longitude" => 106.81137024742438,
                "isPrimary" => 1,
                "id_user" => 1
        ]);
    }
}

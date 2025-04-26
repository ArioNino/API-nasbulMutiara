<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'produk_id';
    protected $fillable = [
        'id_user',
        'nama_produk',
        'kategori',
        'ukuran',
        'deskripsi',
        'harga',
        'gambar',
        'stok'
    ];
}

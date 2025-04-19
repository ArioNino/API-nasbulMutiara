<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi_item';
    protected $primaryKey = 'transaksi_item_id';
    protected $fillable = [
        'id_transaksi',
        'id_produk',
        'id_user',
        'israted',
        'quantity'
    ];
}

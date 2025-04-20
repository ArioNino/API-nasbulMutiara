<?php

namespace App\Models;

use App\Models\User;
use App\Models\Produk;
use App\Models\Rating;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keranjang extends Model
{
    use HasFactory;
    protected $table = 'keranjang';
    protected $primaryKey = 'keranjang_id';
    protected $fillable = [
        'id_transaksi',
        'id_produk',
        'id_user',
        'israted',
        'quantity'
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'produk_id');
    }
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'transaksi_id');
    }
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class, 'id_keranjang', 'keranjang_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'user_id');
    }

}

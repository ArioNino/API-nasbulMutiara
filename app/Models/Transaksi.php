<?php

namespace App\Models;

use App\Models\Alamat;
use App\Models\Keranjang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    protected $fillable = [
        'total',
        'status',
        'id_alamat',
        'jenis_pembayaran',
        'snaptoken'
    ];

    public function Keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_transaksi', 'transaksi_id');
    }

    public function alamat(): belongsTo
    {
        return $this->belongsTo(Alamat::class, 'id_alamat', 'alamat_id');
    }
}

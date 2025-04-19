<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'rating';
    protected $primaryKey = 'rating_id';
    protected $fillable = [
        'id_user',
        'id_transaksi_item',
        'rating',
        'comment',
        'gambar'
    ];
    // public function transaksiItem(): BelongsTo
    // {
    //     return $this->belongsTo(TransaksiItem::class, 'id_transaksi_item', 'transaksi_item_id');
    // }
}

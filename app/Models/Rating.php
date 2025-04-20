<?php

namespace App\Models;

use App\Models\Keranjang;
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
        'id_keranjang',
        'rating',
        'comment',
        'gambar'
    ];
    public function Keranjang(): BelongsTo
    {
        return $this->belongsTo(Keranjang::class, 'id_keranjang', 'keranjang_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alamat extends Model
{
    use HasFactory;

    protected $table = 'alamat';
    protected $primaryKey = 'alamat_id';
    protected $fillable = [
        'id_user',
        'no_telepon',
        'label_alamat',
        'nama_penerima',
        'detail',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'latitude',
        'longitude',
        'isPrimary',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'user_id');
    }
}

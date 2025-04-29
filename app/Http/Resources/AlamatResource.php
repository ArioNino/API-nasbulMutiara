<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlamatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'alamat_id' => $this->alamat_id,
            'label_alamat' => $this->label_alamat,
            'nama_penerima' => $this->nama_penerima,
            'no_telepon' => $this->no_telepon,
            'detail' => $this->detail,
            'kelurahan' => $this->kelurahan,
            'kecamatan' => $this->kecamatan,
            'kabupaten' => $this->kabupaten,
            'provinsi' => $this->provinsi,
            'isPrimary' => $this->isPrimary,
        ];
    }
}

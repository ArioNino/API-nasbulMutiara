<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KeranjangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->keranjang_id,
            'nama_produk'=>$this->produk->nama_produk,
            'gambar' => $this->produk->gambar,
            'quantity'=>$this->quantity,
            'harga' => $this->produk->harga,
            'ukuran' => $this->produk->ukuran,
            'gambar' => 'http://127.0.0.1:8000/storage/'.$this->produk->gambar
        ];
    }
}

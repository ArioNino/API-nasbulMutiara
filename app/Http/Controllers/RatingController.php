<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Rating;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function unrated(){
        $user = Auth::user();
        $transaksi = Transaksi::where('status', 'delivered')->whereHas('keranjang', function ($query) use ($user) {
            $query->where('id_user', $user->user_id);
        })
        ->with(['keranjang.produk', 'keranjang.rating'])
        ->get();

        $response = $transaksi->map(function ($transaksi) {
            return [
                'transaksi_id' => $transaksi->transaksi_id,
                'status' => $transaksi->status,
                'total_harga' => $transaksi->total,
                'keranjang' => $transaksi->keranjang->map(function ($item) {
                    $itemData = [
                        'keranjang_id' => $item->keranjang_id,
                        'israted' => $item->israted,
                        'produk_id' => $item->produk->produk_id,
                        'nama_produk' => $item->produk->nama_produk,
                        'ukuran' => $item->produk->ukuran,
                        'harga' => $item->produk->harga,
                        'quantity' => $item->quantity,
                    ];

                    if ($item->israted == 1 && $item->rating) {
                        $itemData['rating'] = [
                            'rating_value' => $item->rating->rating,
                            'comment' => $item->rating->comment,
                        ];
                    }

                    return $itemData;
                }),
            ];
        });
        return response()->json($response);
    }

    // Masih bug
    public function rating(Request $request, $id){
        $user = Auth::user();
        $data = $request->validate([
            'rating' => "required"
        ]);
        $keranjang = Keranjang::with('transaksi')->find($id);
        if(!$keranjang->id_transaksi){
            return response()->json([
                'message' => 'Produk belum dibeli'
            ], 405);
        }else{
            if ($keranjang->isRated == true) {
                return response()->json([
                    'message' => 'Sudah dirating.'
                ], 405);
            }else{
                if($keranjang->transaksi->status == 'delivered'){
                    $rating = Rating::create([
                        'id_user'=>$user->user_id,
                        'id_transaksi_item'=> $id,
                        'rating'=>$request->rating,
                        'comment'=>$request->comment,
                    ]);
                    $keranjang->update([
                        'israted' => true
                    ]);
                    return response()->json([
                        'message' => 'Rating berhasil ditambahkan'
                    ]);
                }
                else{
                    return response()->json([
                        'message' => 'Pastikan makananmu sudah sampai yaa:)'
                    ], 405);
                }
            }
        }
    }

    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

}

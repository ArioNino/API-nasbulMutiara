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

    public function rating(Request $request, $keranjang_id) {
        $user = Auth::user();

        // Validasi data input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5', // rating antara 1 dan 5
            'comment' => 'nullable|string|max:255', // komentar opsional
        ]);

        // Cek apakah keranjang milik user yang sedang login
        $keranjang = Keranjang::where('keranjang_id', $keranjang_id)
            ->whereHas('transaksi', function ($query) use ($user) {
                $query->where('status', 'delivered')
                      ->where('id_user', $user->user_id);
            })
            ->first();

        // Jika keranjang tidak ditemukan atau tidak milik user, return error
        if (!$keranjang) {
            return response()->json(['message' => 'Keranjang tidak ditemukan atau bukan milik anda.'], 404);
        }

        // Menyimpan rating dan komentar
        $rating = new Rating();
        $rating->keranjang_id = $keranjang->keranjang_id;
        $rating->rating = $request->rating;
        $rating->comment = $request->comment;
        $rating->save();

        // Update status israted pada keranjang menjadi 1
        $keranjang->israted = 1;
        $keranjang->save();

        // Mengembalikan response sukses
        return response()->json([
            'message' => 'Rating berhasil diberikan!',
            'rating' => [
                'rating_value' => $rating->rating,
                'comment' => $rating->comment
            ]
        ]);
    }


    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

}

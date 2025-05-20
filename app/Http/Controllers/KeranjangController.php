<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\KeranjangResource;

class KeranjangController extends Controller
{
    public function add(Request $request)
    {
        $user = Auth::user();
        $cari = Keranjang::where('id_user', $user->user_id)
            ->where('id_produk', $request->id_produk)
            ->where('id_transaksi', null)
            ->first();

        if ($cari === null) {
            $data = Keranjang::create([
                'id_user' => $user->user_id,
                'id_produk' => $request->id_produk,
                'israted' => false,
                'quantity' => $request->quantity,
            ]);
            return response()->json([
                'message' => 'Item telah ditambahkan',
                'data' => $data
            ]);
        } else {
            $cari->update([
                'quantity' => $cari->quantity + $request->quantity
            ]);
            return response()->json([
                'message' => 'Item telah diperbarui',
                'data' => $cari
            ]);
        }
    }

    public function show()
    {
        $user = Auth::user();
        $cari = Keranjang::where('id_user', $user->user_id)->where('id_transaksi', null)->get();
        return response()->json(KeranjangResource::collection($cari));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_item' => 'required',
            'action' => 'required',
        ]);

        $user = Auth::user();
        $keranjang = Keranjang::where('keranjang_id', $request->id_item)
            ->where('id_user', $user->user_id)
            ->whereNull('id_transaksi')
            ->first();

        if (!$keranjang) {
            return response()->json([
                'message' => 'Item tidak ditemukan di keranjang'
            ], 404);
        }

        if ($request->action === 'increase') {
            $keranjang->quantity += 1;
        } elseif ($request->action === 'decrease') {
            if ($keranjang->quantity > 1) {
                $keranjang->quantity -= 1;
            } else {
                return response()->json([
                    'message' => 'Quantity tidak boleh kurang dari 1'
                ], 400);
            }
        }

        $keranjang->save();

        return response()->json([
            'message' => 'Quantity berhasil diperbarui',
            'data' => $keranjang
        ]);
    }

    public function delete(Request $request)
    {
        foreach ($request->id_item as $id_item) {
            $cari = Keranjang::findOrFail($id_item);
            $cari->delete();
        }
        return response()->json([
            'message' => 'Item berhasil dihapus dari keranjang'
        ]);
    }
}


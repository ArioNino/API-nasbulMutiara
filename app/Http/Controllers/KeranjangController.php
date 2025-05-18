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

        if ($cari === null) { // Pengecekan jika item belum ada di keranjang
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
        } else { // Jika item sudah ada di keranjang
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
            'id_item' => 'required|exists:keranjang,id',
            'action' => 'required|in:increase,decrease',
        ]);

        $item = Keranjang::findOrFail($request->id_item);

        if ($request->action === 'increase') {
            $item->quantity += 1;
        } elseif ($request->action === 'decrease') {
            $item->quantity -= 1;
            if ($item->quantity <= 0) {
                $item->delete();
                return response()->json([
                    'message' => 'Item dihapus karena quantity menjadi 0'
                ]);
            }
        }

        $item->save();

        return response()->json([
            'message' => 'Jumlah item berhasil diperbarui',
            'data' => $item
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

    // public function delete($id)
    // {
    //     $produk = Keranjang::findOrFail($id);
    //     $produk->delete();
    //     return response()->json(['message' => 'Keranjang berhasil dihapus']);
    // }
}


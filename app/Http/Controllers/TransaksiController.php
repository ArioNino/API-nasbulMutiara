<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'total' => 'required',
            'id_alamat' => 'required',
            'jenis_pembayaran' => 'required'
        ]);

        foreach ($request->id_item as $id_item) {
            $cari = Keranjang::findOrFail($id_item);
            if ($cari->id_transaksi) {
                return response()->json([
                    'message' => 'Item sudah memiliki transaksi.'
                ], 405);
            }
            $produk = $cari->produk;
            $stockBaru = $produk->stok - $cari->quantity;
            if ($stockBaru < 0) {
                return response()->json([
                    'message' => 'Ada barang yang stoknya habis'
                ], 400);
            }
            $produk->update([
                'stok' => $stockBaru
            ]);
        }
        if ($request->jenis_pembayaran == 'transfer') {
            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction');
            Config::$isSanitized = config('midtrans.isSanitized');
            Config::$is3ds = config('midtrans.is3ds');

            $transaction_details = array(
                'order_id' => rand(),
                'gross_amount' => $request->total
            );
            $customer_details = array(
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email
            );
            $params = array(
                'transaction_details' => $transaction_details,
                'customer_details' => $customer_details
            );

            $snapToken = Snap::getSnapToken($params);
            $transaksi = Transaksi::create([
                'total' => $request->total,
                'status' => 'pending',
                'jenis_pembayaran' => 'transfer',
                'id_alamat' => $request->id_alamat,
                'snaptoken' => $snapToken
            ]);
        }

        else{
             $transaksi = Transaksi::create([
                'total' => $request->total,
                'status' => 'pending',
                'jenis_pembayaran' => 'Tunai',
                'id_alamat' => $request->id_alamat,
                'snaptoken' => 'COD'
            ]);
        }

        foreach ($request->id_item as $id_item) {
            $cari = Keranjang::findOrFail($id_item);
            $cari->update([
                'id_transaksi' => $transaksi->transaksi_id
            ]);
        }
        return response()->json([
            'transaksi_id' => $transaksi->transaksi_id,
            'snaptoken' => $transaksi->snaptoken
        ]);
    }

    public function berhasil($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status' => 'Paid'
        ]);
        return response()->json([
            'message' => 'Transaksi berhasil dibayar'
        ]);
    }

    public function gagal($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status' => 'failed'
        ]);
        return response()->json([
            'message' => 'Transaksi digagalkan'
        ]);
    }

    public function masak($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status' => 'on process'
        ]);
        return response()->json([
            'message' => 'Makanan sedang dibuat dengan sepenuh hati:)'
        ]);
    }

    public function otw($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status' => 'on deliver'
        ]);
        return response()->json([
            'message' => 'Makanan sedang dikirim'
        ]);
    }

    public function sampai($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status' => 'delivered'
        ]);
        return response()->json([
            'message' => 'Makanan sudah sampai ke tempat tujuan'
        ]);
    }
}

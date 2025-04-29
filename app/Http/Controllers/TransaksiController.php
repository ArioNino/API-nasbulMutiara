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
            'id_alamat' => 'required'
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
            'id_alamat' => $request->id_alamat,
            'snaptoken' => $snapToken
        ]);
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
            'status' => 'success'
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

    public function sampai($id){
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status'=>'delivered'
        ]);
        return response()->json([
            'message' => 'Makanan sudah sampai ke tempat tujuan'
        ]);
    }

    #Customer dashboard MY ORDER
    public function show()
    {
        $user = Auth::user();
        $transaksi = Transaksi::whereHas('keranjang', function ($query) use ($user) {
            $query->where('id_user', $user->user_id);
        })
            ->with(['keranjang.produk'])
            ->latest()->get();


        $response = $transaksi->map(function ($transaksi) {
            return [
                'transaksi_id' => $transaksi->transaksi_id,
                'status' => $transaksi->status,
                'total_harga' => $transaksi->total,
                'keranjang' => $transaksi->keranjang->map(function ($item) {
                    return [
                        'keranjang_id' => $item->keranjang_id,
                        'produk_id' => $item->produk->produk_id,
                        'nama_produk' => $item->produk->nama_produk,
                        'ukuran' => $item->produk->ukuran,
                        'israted' => $item->israted,
                        'harga' => $item->produk->harga,
                        'quantity' => $item->quantity,
                        'gambar' => url('/storage/' . $item->produk->gambar)
                    ];
                }),
            ];
        });

        return response()->json($response);
    }

    // Admin dashboard bagian ORDER
    public function all()
    {
        $transaksi = Transaksi::with(['keranjang.produk', 'alamat'])
            ->latest()
            ->get();

        $response = $transaksi->map(function ($transaksi) {
            return [
                'transaksi_id' => $transaksi->transaksi_id,
                'tanggal_pembelian' => $transaksi->created_at->format('d-M-Y h:i'),
                'nama_pembeli' => $transaksi->alamat->nama_penerima,
                'total_harga' => $transaksi->total,
                'status' => $transaksi->status
            ];
        });

        return response()->json($response);
    }

    // Admin dashboard bagian CUSTOMER
    public function customer() {
        $user = Auth::user();
        $transaksi = Transaksi::with(['alamat'])
                              ->whereHas('keranjang', function ($query) use ($user) {
                                  $query->where('id_user', $user->user_id);
                              })
                              ->latest()
                              ->get();

        if ($transaksi->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada transaksi ditemukan untuk pengguna ini.'
            ], 404);
        }

        $totalSpent = $transaksi->sum('total');
        $lastTransaction = $transaksi->first();

        $response = [
            'nama_pembeli' => $lastTransaction->alamat->nama_penerima,
            'total_spent' => $totalSpent,
            'last_transaction' => [
                'total_harga' => $lastTransaction->total,
            ],
        ];

        return response()->json($response);
    }
}

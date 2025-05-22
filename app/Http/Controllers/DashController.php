<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use App\Models\Alamat;

class DashController extends Controller
{
    // Admin Dashboard
    public function index()
    {
        $totalTransaksi = Transaksi::count();
        $transaksiDelivered = Transaksi::where('status', 'delivered')->count();
        $totalNominal = Transaksi::sum('total');
        $totalCustomer = Keranjang::whereHas('transaksi', function ($query) {
            $query->whereNotNull('id_transaksi');
        })
            ->distinct('id_user')
            ->count('id_user');

        return response()->json([
            'income_money' => $totalNominal,
            'total_order' => $totalTransaksi,
            'total_delivered' => $transaksiDelivered,
            'total_customer' => $totalCustomer,
        ]);
    }

    // Admin dashboard bagian ORDER
    public function order()
    {
        $transaksi = Transaksi::with(['keranjang.produk', 'alamat'])
            ->latest()
            ->get();

        $response = $transaksi->map(function ($transaksi) {
            return [
                'transaksi_id' => $transaksi->transaksi_id,
                'tanggal_pembelian' => $transaksi->created_at->format('d-M-Y H:i'),
                'nama_pembeli' => $transaksi->alamat->nama_penerima,
                'total_harga' => $transaksi->total,
                'alamat' => $transaksi->alamat,
                'status' => $transaksi->status
            ];
        });

        return response()->json($response);
    }

    //Admin Dashboard bagian Customer
    public function customer()
    {
        $transaksi = Transaksi::with(['keranjang.produk', 'alamat']) // ambil semua alamat (termasuk non-primary)
            ->where('status', 'delivered')
            ->latest()
            ->get();

        $Customers = [];

        foreach ($transaksi as $item) {
            $alamat = $item->alamat;

            if (!$alamat || $alamat->isPrimary != 1) {
                if ($alamat && $alamat->id_user) {
                    $alamatUtama = Alamat::where('id_user', $alamat->id_user)
                        ->where('isPrimary', 1)
                        ->first();

                    if ($alamatUtama) {
                        $alamat = $alamatUtama;
                    }
                }
            }

            if (!$alamat)
                continue;

            $namaPenerima = $alamat->nama_penerima;

            $alamatDetail = $alamat->detail;
            $kelurahan = $alamat->kelurahan;
            $kecamatan = $alamat->kecamatan;
            $kabupaten = $alamat->kabupaten;
            $provinsi = $alamat->provinsi;

            $alamatLengkap = "{$alamatDetail} {$kelurahan} {$kecamatan} {$kabupaten} {$provinsi}";

            if (!isset($Customers[$namaPenerima])) {
                $Customers[$namaPenerima] = [
                    'Nama' => $namaPenerima,
                    'Alamat' => $alamatLengkap,
                    'total_spent' => 0,
                    'last_spent' => $item->total,
                ];
            }

            $Customers[$namaPenerima]['total_spent'] += $item->total;
        }

        $response = array_values($Customers);
        return response()->json($response);
    }

    #Customer dashboard MY ORDER
    public function myOrder()
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
                'tanggal_pembelian' => $transaksi->created_at->format('d-M-Y H:i'),
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

    public function orderDetail($id)
    {
        $user = Auth::user();

        $transaksi = Transaksi::where('transaksi_id', $id)
            ->whereHas('keranjang', function ($query) use ($user) {
                $query->where('id_user', $user->user_id);
            })
            ->with(['keranjang.produk'])
            ->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan atau tidak milik Anda.'], 404);
        }

        $response = [
            'transaksi_id' => $transaksi->transaksi_id,
            'status' => $transaksi->status,
            'tanggal_pembelian' => $transaksi->created_at->format('d-M-Y h:i'),
            'jenis_pembayaran' => $transaksi->jenis_pembayaran,
            'total_harga' => $transaksi->total,
            'keranjang' => $transaksi->keranjang->map(function ($item) {
                return [
                    'produk_id' => $item->produk->produk_id,
                    'nama_produk' => $item->produk->nama_produk,
                    'ukuran' => $item->produk->ukuran,
                    'harga' => $item->produk->harga,
                    'quantity' => $item->quantity,
                    'gambar' => url('/storage/' . $item->produk->gambar),
                ];
            }),
        ];

        return response()->json($response);
    }
}

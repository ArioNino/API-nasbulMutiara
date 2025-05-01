<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\User;

class DashController extends Controller
{
    public function index()
    {
        $totalTransaksi = Transaksi::count();
        $transaksiDelivered = Transaksi::where('status', 'delivered')->count();
        // $totalCustomer = User::whereHas('alamat.transaksi')->count();
        $totalNominal = Transaksi::sum('total');

        return response()->json([
            'income_money' => $totalNominal,
            'total_order' => $totalTransaksi,
            'total_delivered' => $transaksiDelivered,
            // 'total_customer' => $totalCustomer,
        ]);
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

    //Admin Dashboard bagian Customer
    public function customer()
    {
        $transaksi = Transaksi::with(['keranjang.produk', 'alamat'])
            ->where('status', 'delivered')
            ->latest()
            ->get();

        $Customers = [];

        foreach ($transaksi as $item) {
            $namaPenerima = $item->alamat->nama_penerima;

            $alamatDetail = $item->alamat->detail;
            $kelurahan = $item->alamat->kelurahan;
            $kecamatan = $item->alamat->kecamatan;
            $kabupaten = $item->alamat->kabupaten;
            $provinsi = $item->alamat->provinsi;

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
}

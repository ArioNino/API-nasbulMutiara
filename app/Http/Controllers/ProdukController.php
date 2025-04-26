<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::all();
        return response()->json($produk);
    }

    public function show($id)
    {
    $produk = Produk::find($id);

    if (!$produk) {
        return response()->json(['message' => 'Produk tidak ditemukan'], 404);
    }
    return response()->json($produk);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|string',
            'stok' => 'required|integer',
        ]);

        $produk = Produk::create($validated);
        return response()->json($produk, 201);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|string',
            'stok' => 'required|integer'
        ]);

        $produk->update($request->all());

        return response()->json($produk);
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
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

    public function store(Request $request){
            $data = $request->validate([
                'nama_produk' => 'required',
                'kategori' => 'required',
                'deskripsi' => 'required',
                'ukuran' => 'required',
                'harga' => 'required',
                'stok' => 'required|integer|min:0'
            ]);

            $data = Produk::create([
                'nama_produk' => $request->nama_produk,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'ukuran' => $request->ukuran,
                'harga' => $request->harga,
                'gambar' => null,
                'stok' => $request->stok
            ]);

            if($request->hasFile('gambar')){
                $file = $request->file('gambar');
                $fileName = $this->quickRandom().$data->produk_id.'.'.$file->extension();
                $path = $file->storeAs('produk', $fileName, 'public');
                $data->update([
                    'gambar' => $path
                ]);
            }

            return response()->json([
                'message' => 'Produk berhasil ditambah'
            ]);
    }

    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function update(Request $request, $id){
        $produk = Produk::findOrFail($id);
        $data = $request->validate([
            'nama_produk' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'ukuran' => 'required',
            'harga' => 'required',
            'stok' => 'required|integer|min:0'
        ]);
        if($request->hasFile('gambar')){
            $pathLama = storage_path('app/public/'.$produk->gambar);
            if(File::exists($pathLama)){
                File::delete($pathLama);
                $file = $request->file('gambar');
                $fileName = $this->quickRandom().'.'.$file->extension();
                $path = $file->storeAs('produk', $fileName, 'public');
                $produk->update([
                    'gambar' => $path
                ]);
            }
        }
        $produk->update([
            'nama_produk' => $request->nama_produk,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $request->ukuran,
            'harga' => $request->harga,
            'stok' => $request->stok
        ]);
        return response()->json([
            'message' => 'Produk berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}

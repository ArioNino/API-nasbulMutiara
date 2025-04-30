<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AlamatResource;

class AlamatController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $primaryAlamat = Alamat::where('id_user', $user->user_id)
            ->where('isPrimary', 1)
            ->first();

        if (!$primaryAlamat) {
            return response()->json([
                'message' => 'Kamu belum ada alamat utama'
            ], 403);
        }
        $otherAlamat = Alamat::where('id_user', $user->user_id)
            ->where('isPrimary', 0)
            ->get();

        $allAlamat = collect([$primaryAlamat])->merge($otherAlamat);

        return response()->json(($allAlamat));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label_alamat' => 'required',
            'no_telepon' => 'required',
            'detail' => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'kabupaten' => 'required',
            'provinsi' => 'required',
        ]);

        $user = Auth::user();
        $cari = Alamat::where('id_user', $user->user_id)->exists();

        if (!$cari) {
            Alamat::create([
                'id_user' => $user->user_id,
                'label_alamat' => $data['label_alamat'],
                'nama_penerima' => $user->name,
                'no_telepon' => $data['no_telepon'],
                'detail' => $data['detail'],
                'kelurahan' => $data['kelurahan'],
                'kecamatan' => $data['kecamatan'],
                'kabupaten' => $data['kabupaten'],
                'provinsi' => $data['provinsi'],
                'isPrimary' => 1,
            ]);
        } else {
            Alamat::create([
                'id_user' => $user->user_id,
                'label_alamat' => $data['label_alamat'],
                'nama_penerima' => $user->name,
                'no_telepon' => $data['no_telepon'],
                'detail' => $data['detail'],
                'kelurahan' => $data['kelurahan'],
                'kecamatan' => $data['kecamatan'],
                'kabupaten' => $data['kabupaten'],
                'provinsi' => $data['provinsi'],
                'isPrimary' => 0,
            ]);
        }

        return response()->json([
            'message' => 'Alamat berhasil ditambah'
        ]);
    }


    public function update(Request $request, $id)
    {
        $cari = Alamat::findOrFail($id);
        $data = $request->validate([
            'label_alamat' => 'required',
            'no_telepon' => 'required',
            'detail' => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'kabupaten' => 'required',
            'provinsi' => 'required',
            'isPrimary' => 'integer',
            'catatan_kurir' => 'nullable|string',
        ]);

        $cari->update($request->all());
        return response()->json([
            'message' => 'Alamat berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $cari = Alamat::findOrFail($id);
        if ($cari->isPrimary == false) {
            $cari->delete();
            return response()->json([
                'message' => 'Alamat berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'message' => 'Alamat utama tidak dapat dihapus'
            ]);
        }
    }

    public function utama($id)
    {
        $user = Auth::user();
        $cari = Alamat::where('alamat_id', $id)->first();
        $primary = Alamat::where('id_user', $user->user_id)->where('isPrimary', true)->first();
        $cari->update([
            'isPrimary' => true
        ]);
        $primary->update([
            'isPrimary' => false
        ]);
        return response()->json([
            'message' => 'Alamat utama telah diganti'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
            'name' => 'required',
        ]);

        if (User::where('username', $request->username)->exists()) {
            return response()->json([
                'message' => 'Username sudah terpakai'
            ], 400);
        }

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email sudah terpakai'
            ], 400);
        }

        User::create([
            'role' => 'pembeli',
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'picture' => null,
        ]);

        return response()->json([
            'message' => 'Akun berhasil dibuat'
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau Password salah.'
            ], 401);
        }

        $token = $user->createToken($request->username)->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => [
                'username' => $user->username,
                'role' => $user->role,
                'email' => $user->email,
                'name' => $user->name,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'picture' => $user->picture ? url('/storage/' . $user->picture) : null
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }

    public function aboutMe()
    {
        $user = auth::user();
        return response()->json([
            'username' => $user->username,
            'role' => $user->role,
            'email' => $user->email,
            'name' => $user->name,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'picture' => $user->picture ? url('/storage/' . $user->picture) : null
        ]);
    }

     public function updatePhoto(Request $request)
    {
        $user = Auth::user();
        if ($request->hasFile('picture')) {
            $pathLama = storage_path('app/public/' . $user->picture);
            if (File::exists($pathLama)) {
                File::delete($pathLama);
            }
            $file = $request->file('picture');
            $fileName = $this->quickRandom() . '.' . $file->extension();
            $path = $file->storeAs('foto_profile', $fileName, 'public');
            $user->update([
                'picture' => $path
            ]);
            return response()->json([
                'message' => 'Foto profile berhasil di-update'
            ]);
        } else {
            return response()->json([
                'message' => 'Foto profile gagal dii-update'
            ], 500);
        }
    }

     public function update(Request $request)
    {
        $user = Auth::user();
        $request_data = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'gender' => 'required',
            'phone' => 'required'
        ]);
        $user->update($request_data);
        return response()->json([
            'message' => 'Data profile telah diupdate'
        ]);
    }

    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}

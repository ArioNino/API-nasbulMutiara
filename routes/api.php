<?php

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\alamatController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\TransaksiController;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['admin'])->group(function () {
        Route::post('/produk/add', [ProdukController::class, 'store']);
        Route::post('/produk/update/{id}', [ProdukController::class, 'update']);
        Route::delete('/produk/delete/{id}', [ProdukController::class, 'destroy']);
    });
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/alamat', [AlamatController::class, 'show']);
    Route::post('/alamat/add', [AlamatController::class, 'store']);
    Route::post('/alamat/{id}', [AlamatController::class, 'update']);
    Route::delete('/alamat/delete/{id}', [AlamatController::class, 'destroy']);
    Route::get('/alamat/primary/{id}', [AlamatController::class, 'utama']);

    Route::get('/keranjang', [KeranjangController::class, 'show']);
    Route::post('/keranjang/add', [KeranjangController::class, 'add']);
    Route::delete('/keranjang/delete/{id}', [KeranjangController::class, 'delete']);

    Route::post('/bayar', [TransaksiController::class, 'store']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);


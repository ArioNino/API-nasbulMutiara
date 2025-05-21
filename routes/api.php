<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\alamatController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TransaksiController;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['admin'])->group(function () {
        Route::post('/produk/add', [ProdukController::class, 'store']);
        Route::post('/produk/update/{id}', [ProdukController::class, 'update']);
        Route::delete('/produk/delete/{id}', [ProdukController::class, 'destroy']);

        Route::get('/dashboard', [DashController::class, 'index']);
        Route::get('/dashboard/order', [DashController::class, 'order']);
        Route::get('/dashboard/customer', [DashController::class, 'customer']);
    });
    Route::get('/dashboard/kurir', [DashController::class, 'kurir'])->middleware('kurir');

    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/aboutMe', [AuthController::class, 'aboutMe']);
    Route::post('/aboutMe/update', [AuthController::class, 'update']);
    Route::post('/aboutMe/updatePhoto', [AuthController::class, 'updatePhoto']);

    Route::get('/alamat', [AlamatController::class, 'show']);
    Route::post('/alamat/add', [AlamatController::class, 'store']);
    Route::post('/alamat/update/{id}', [AlamatController::class, 'update']);
    Route::delete('/alamat/delete/{id}', [AlamatController::class, 'destroy']);
    Route::get('/alamat/primary/{id}', [AlamatController::class, 'utama']);

    Route::get('/keranjang', [KeranjangController::class, 'show']);
    Route::post('/keranjang/add', [KeranjangController::class, 'add']);
    Route::delete('/keranjang/delete', [KeranjangController::class, 'delete']);
    Route::post('/keranjang/update', [KeranjangController::class, 'update']);

    Route::get('/ulasan', [RatingController::class, 'unrated']);
    Route::post('/ulasan/{id}', [RatingController::class, 'rating']);

    Route::post('/bayar', [TransaksiController::class, 'store']);
    Route::get('/bayar/berhasil/{id}', [TransaksiController::class, 'berhasil']);
    Route::get('/bayar/gagal/{id}', [TransaksiController::class, 'gagal']);
    Route::get('/masak/{id}', [TransaksiController::class, 'masak'])->middleware('admin');
    Route::get('/otw/{id}', [TransaksiController::class, 'otw'])->middleware('admin');
    Route::get('/sampai/{id}', [TransaksiController::class, 'sampai']);

    Route::get('/transaksi', [DashController::class, 'myOrder']); //dashboard customer liat order
    Route::get('/transaksi/{id}', [DashController::class, 'orderDetail']); //dashboard customer liat order
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);

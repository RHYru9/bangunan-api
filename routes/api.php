<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BarangController;



Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function () {
    Route::post('masuk', [AuthController::class, 'masuk']);
    Route::post('daftar', [AuthController::class, 'daftar']);
    Route::get('users', [AuthController::class, 'listuser']);
    Route::delete('user/{id}', [AuthController::class, 'hapus']);
    //bagian admin
    Route::get('admins' ,[AdminController::class, 'listAdmin']);
    Route::post('admin/daftar', [AdminController::class, 'daftarAdmin']);
    Route::delete('admin/delete/{id}', [AdminController::class, 'hapusAdmin']);
    Route::post('admin/login', [AdminController::class, 'masukAdmin']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'produk',
], function () {
    Route::get('barang-list', [BarangController::class, 'listBarang']);
    Route::post('barang-tambah', [BarangController::class, 'tambahbarang']);
    Route::delete('barang-hapus/{id}', [BarangController::class, 'hapusbarang']);
});

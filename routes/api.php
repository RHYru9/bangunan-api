<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BarangController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function () {
    Route::post('masuk', [UsersController::class, 'masuk']);
    Route::post('daftar', [UsersController::class, 'daftar']);
    Route::get('users', [UsersController::class, 'listUsersByRole'])->middleware('role:admin');
    Route::delete('users/{role}/{id}', [UsersController::class, 'hapusByRole'])->middleware('role:admin');
    Route::post('admin/daftar', [UsersController::class, 'daftarAdmin'])->middleware('role:admin');

    Route::post('keluar', [UsersController::class, 'keluar'])->middleware('auth:api');
    Route::post('refresh', [UsersController::class, 'refresh'])->middleware('auth:api');
    Route::get('saya', [UsersController::class, 'saya'])->middleware('auth:api');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'produk',
], function () {
    Route::get('barang-list', [BarangController::class, 'listBarang']);
    Route::post('barang-tambah', [BarangController::class, 'tambahbarang'])->middleware('role:admin,karyawan');
    Route::post('barang-edit/{id}', [BarangController::class, 'editbarang'])->middleware('role:admin,karyawan');
    Route::delete('barang-hapus/{id}', [BarangController::class, 'hapusbarang'])->middleware('role:admin,karyawan');
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function () {
    Route::post('masuk', [AuthController::class, 'masuk']);
    Route::post('daftar', [AuthController::class, 'daftar']);
    Route::get('users', [AuthController::class, 'listuser']);
    Route::delete('user/{id}', [AuthController::class, 'hapus']);
});

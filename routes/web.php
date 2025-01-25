<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['guest'])->group(function () {
    Route::get('auth', [AuthController::class, 'auth'])->name('auth');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

// untuk mencegah belum login sudah di atasi oleh EnsureHasRole.php
Route::get('admin', [AdminController::class, 'index'])->name('admin')->middleware('role:admin');
Route::get('user', [UserController::class, 'index'])->name('user')->middleware('role:user');

Route::middleware('auth')->group(function () {
    Route::get('sew', [UserController::class, 'sew'])->name('sew');
    Route::get('cake', [UserController::class, 'cake'])->name('cake');
    Route::post('create', [UserController::class, 'create'])->name('create');

    Route::get('jadwal', [UserController::class, 'jadwal'])->name('jadwal');
    Route::get('laporan', [UserController::class, 'laporan'])->name('laporan');

    Route::post('generate-laporan', [UserController::class, 'generateLaporanPdf'])->name('generateLaporanPdf');

});

Route::post('logout', [AuthController::class, 'logout'])->name('logout');
// smtp
Route::get('verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');

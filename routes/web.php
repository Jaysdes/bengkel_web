<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\ProsesController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SCController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SP_Controller;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MekanikController;

// Rute login & logout
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rute debug session
Route::get('/debug-session', function () {
    return session()->all();
});


// Rute hanya untuk user yang login
Route::middleware('auth.session')->group(function () {

    // Dashboard utama
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rute Sidebar Tab

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/data', [DataController::class, 'index'])->name('data');
    Route::get('/teknisi', [TeknisiController::class, 'index'])->name('teknisi');
    Route::get('/proses', [ProsesController::class, 'index'])->name('proses');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/service-center', [SCController::class, 'index'])->name('service-center');
    Route::get('/sparepart', [SP_Controller::class, 'index'])->name('sparepart');
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::get('/mekanik', [MekanikController::class, 'index'])->name('mekanik');


    
    // Manajemen user (tampilan page)
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

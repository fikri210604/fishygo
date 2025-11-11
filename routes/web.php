<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\JenisIkanController;
use App\Http\Controllers\Admin\KategoriProdukController;
use App\Http\Controllers\ArticlePublicController;
use App\Http\Controllers\HomeController;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Artikel publik
Route::get('/articles', [ArticlePublicController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticlePublicController::class, 'show'])->name('articles.show');

// Dashboard user
Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

// Detail produk publik (pakai slug)
Route::get('/produk/{produk:slug}', [ProdukController::class, 'show'])->name('produk.show');

// Profil user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard akses role
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth','verified','can:access-admin'])
        ->name('admin.dashboard');

    Route::view('/kurir/dashboard', 'kurir.dashboard')
        ->middleware('can:access-kurir')
        ->name('kurir.dashboard');

    Route::prefix('admin')->middleware('can:access-admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
        Route::resource('admins', AdminManagementController::class)->parameters(['admins' => 'admin'])->except(['show']);
        Route::resource('articles', ArticleController::class)->parameters(['articles' => 'article'])->except(['show']);

        Route::post('uploads/rte', [UploadController::class, 'rte'])->name('uploads.rte');

        // Master data
        Route::resource('kategori', KategoriProdukController::class)->parameters(['kategori' => 'kategori'])->except(['show']);
        Route::resource('jenis-ikan', JenisIkanController::class)->parameters(['jenis-ikan' => 'jenis_ikan'])->except(['show']);
        Route::resource('produk', ProdukController::class)->parameters(['produk' => 'produk'])->except(['show']);
    });
});

// Import route auth.php
require __DIR__.'/auth.php';

// (Disederhanakan) endpoint polling tidak diperlukan

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\PesananManajementController;
use App\Http\Controllers\DetailProdukController;
use App\Http\Controllers\ReviewProdukController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\JenisIkanController;
use App\Http\Controllers\Admin\KategoriProdukController;
use App\Http\Controllers\ArticlePublicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\Kurir\DeliveryController as KurirDeliveryController;
use App\Http\Controllers\Kurir\DashboardController as KurirDashboardController;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Halaman tentang
Route::view('/tentang', 'tentang')->name('tentang');

// Midtrans redirection endpoints (Finish/Unfinish/Error)
Route::get('/payment/midtrans/finish', [PembayaranController::class, 'midtransFinish'])->name('payment.midtrans.finish');
Route::get('/payment/midtrans/unfinish', [PembayaranController::class, 'midtransUnfinish'])->name('payment.midtrans.unfinish');
Route::get('/payment/midtrans/error', [PembayaranController::class, 'midtransError'])->name('payment.midtrans.error');

// Artikel publik
Route::get('/articles', [ArticlePublicController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticlePublicController::class, 'show'])->name('articles.show');

// Dashboard user (daftar produk)
Route::get('/produk', [HomeController::class, 'index'])
    ->middleware(['auth'])
    ->name('home');

// Detail produk publik (pakai slug)
Route::get('/produk/{produk:slug}', [DetailProdukController::class, 'show'])->name('produk.show');

// Profil user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Keranjang belanja
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{produk:produk_id}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/keranjang/{produk:produk_id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{produk:produk_id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/keranjang', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout & Pesanan
    Route::get('/checkout', [PesananController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [PesananController::class, 'store'])->name('checkout.store');
    // Midtrans Snap token (user-initiated)
    Route::post('/payment/midtrans/snap', [PembayaranController::class, 'midtransSnap'])->name('payment.midtrans.snap');
    // Pastikan rute statis didefinisikan sebelum parameter agar tidak bentrok
    Route::get('/pesanan/history', [PesananController::class, 'history'])->name('pesanan.history');
    Route::get('/pesanan/{pesanan:pesanan_id}', [PesananController::class, 'show'])
        ->whereUuid('pesanan')
        ->name('pesanan.show');
    // Upload bukti transfer manual
    Route::post('/pesanan/{pesanan:pesanan_id}/manual/upload', [PembayaranController::class, 'manualUpload'])
        ->whereUuid('pesanan')
        ->name('pesanan.manual.upload');
    Route::get('/pesanan/{pesanan:pesanan_id}/receipt', [PembayaranController::class, 'receiptUser'])
        ->whereUuid('pesanan')
        ->name('pesanan.receipt');
    Route::post('/pesanan/{pesanan:pesanan_id}/cancel', [PesananController::class, 'cancel'])
        ->whereUuid('pesanan')
        ->name('pesanan.cancel');
});

// Dashboard akses role (admin requires verified)
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('can:access-admin')
        ->name('admin.dashboard');

    Route::prefix('admin')->middleware('can:access-admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
        Route::resource('admins', AdminManagementController::class)->parameters(['admins' => 'admin'])->except(['show']);
        Route::resource('articles', ArticleController::class)->parameters(['articles' => 'article'])->except(['show']);

        Route::post('uploads/rte', [UploadController::class, 'rte'])->name('uploads.rte');

        // Master data
        Route::resource('kategori', KategoriProdukController::class)->parameters(['kategori' => 'kategori'])->except(['show']);
        Route::resource('jenis-ikan', JenisIkanController::class)->parameters(['jenis-ikan' => 'jenis_ikan'])->except(['show']);
        Route::resource('produk', ProdukController::class)->parameters(['produk' => 'produk'])->except(['show']);

        // Settings: Roles & Permissions
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::resource('permissions', PermissionController::class)->except(['show','create','edit']);
            Route::resource('roles', RolePermissionController::class)->except(['show','create','edit']);
            Route::put('roles/{role}/permissions', [RolePermissionController::class, 'updatePermissions'])->name('roles.permissions.update');
        });

        // Pesanan (Admin)
        Route::resource('pesanan', PesananManajementController::class)->only(['index','show']);
        // Pembayaran COD actions
        Route::post('pesanan/{pesanan:pesanan_id}/cod/confirm', [\App\Http\Controllers\PembayaranController::class, 'codConfirm'])
            ->whereUuid('pesanan')->name('pesanan.cod.confirm');
        Route::post('pesanan/{pesanan:pesanan_id}/cod/cancel', [\App\Http\Controllers\PembayaranController::class, 'codCancel'])
            ->whereUuid('pesanan')->name('pesanan.cod.cancel');
        // Pembayaran Manual Transfer actions
        Route::post('pesanan/{pesanan:pesanan_id}/manual/confirm', [\App\Http\Controllers\PembayaranController::class, 'manualConfirm'])
            ->whereUuid('pesanan')->name('pesanan.manual.confirm');
        Route::post('pesanan/{pesanan:pesanan_id}/manual/reject', [\App\Http\Controllers\PembayaranController::class, 'manualReject'])
            ->whereUuid('pesanan')->name('pesanan.manual.reject');
        Route::get('pesanan/{pesanan:pesanan_id}/receipt', [\App\Http\Controllers\PembayaranController::class, 'receipt'])
            ->whereUuid('pesanan')->name('pesanan.receipt');
    });
});

// Kurir dashboard (no email verification required)
Route::middleware(['auth'])->prefix('kurir')->name('kurir.')->group(function(){
    Route::middleware('can:access-kurir')->group(function(){
        Route::get('/dashboard', [KurirDashboardController::class, 'index'])->name('dashboard');
        Route::get('/pengiriman', [KurirDeliveryController::class, 'index'])->name('pengiriman.index');
        Route::get('/pengiriman/{pengiriman:pengiriman_id}', [KurirDeliveryController::class, 'show'])
            ->whereUuid('pengiriman')->name('pengiriman.show');
        Route::post('/pengiriman/{pengiriman:pengiriman_id}/status', [KurirDeliveryController::class, 'updateStatus'])
            ->whereUuid('pengiriman')->name('pengiriman.status');
    });
});

// Review produk (user login)
Route::middleware('auth')->group(function () {
    Route::post('/produk/{produk:produk_id}/reviews', [ReviewProdukController::class, 'store'])->name('produk.review.store');
    Route::put('/produk/reviews/{review:review_id}', [ReviewProdukController::class, 'update'])->name('produk.review.update');
    Route::delete('/produk/reviews/{review:review_id}', [ReviewProdukController::class, 'destroy'])->name('produk.review.destroy');
});

// List review produk (public, JSON)
Route::get('/produk/{produk:produk_id}/reviews', [ReviewProdukController::class, 'indexByProduct'])->name('produk.review.index');

// Import route auth.php
require __DIR__.'/auth.php';

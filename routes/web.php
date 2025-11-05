<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\ArticlePublicController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Public article pages (view only)
Route::get('/articles', [ArticlePublicController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticlePublicController::class, 'show'])->name('articles.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/admin/dashboard', 'admin.dashboard')
        ->middleware('can:access-admin')
        ->name('admin.dashboard');
    Route::view('/kurir/dashboard', 'kurir.dashboard')
        ->middleware('can:access-kurir')
        ->name('kurir.dashboard');

    // Admin management routes (resource)
    Route::prefix('admin')->middleware('can:access-admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class)
            ->except(['show']);

        Route::resource('admins', AdminManagementController::class)
            ->parameters(['admins' => 'admin'])
            ->except(['show']);

        Route::resource('articles', ArticleController::class)
            ->parameters(['articles' => 'article'])
            ->except(['show']);

        // Upload endpoint for rich text editor (local, no CDN)
        Route::post('uploads/rte', [UploadController::class, 'rte'])
            ->name('uploads.rte');
    });
});

require __DIR__.'/auth.php';

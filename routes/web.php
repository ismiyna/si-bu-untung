<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelangganAuthController;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangController;
use App\Models\Barang;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'))->name('welcome');

/* ===================== AUTH (Custom) ===================== */
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* ================== REGISTER PELANGGAN =================== */
Route::get('/pelanggan/register', [PelangganAuthController::class, 'showRegisterForm'])
    ->name('pelanggan.register');
Route::post('/pelanggan/register', [PelangganAuthController::class, 'register'])
    ->name('pelanggan.register.store');

/* ===================== STAFF / ADMIN ===================== */
Route::middleware('auth:staff')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    /* ====== Halaman UI sesuai sidebar (mockup) ====== */
    // View: resources/views/barang/tambah.blade.php
    Route::get('/tambah', function () {
        $barang = new Barang();
        return view('barang.tambah', compact('barang'));
    })->name('ui.tambah');

    Route::post('/barang/quick-store', [BarangController::class, 'quickStore'])
        ->name('barang.quick.store');

    // View: resources/views/barang/edit-page.blade.php
    Route::get('/edit', [BarangController::class, 'quickEditPage'])
        ->name('ui.edit');

    Route::post('/barang/quick-update', [BarangController::class, 'quickUpdate'])
        ->name('barang.quick.update');

    // === Tambahan util (AJAX) ===
    // Generate next ID by prefix
    Route::get('/barang/next-id', [BarangController::class, 'nextId'])
        ->name('barang.next-id');

    // Lookup 1 barang by id untuk autofill form edit (dipakai tombol "Pilih")
    Route::get('/barang/find', [BarangController::class, 'findById'])
        ->name('barang.find');
    // ============================

    // View: resources/views/barang/hapus.blade.php
    Route::get('/hapus', function () {
        $q = request('q');
        $barangs = Barang::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where('id_barang', 'like', "%{$q}%")
                   ->orWhere('nama_barang', 'like', "%{$q}%");
            })
            ->orderBy('id_barang')
            ->paginate(8)
            ->withQueryString();

        return view('barang.hapus', compact('barangs', 'q'));
    })->name('ui.hapus');

    /* ====== ROUTE RESOURCE (CRUD DB) ====== */
    Route::resource('barang', BarangController::class)
        // Pastikan getRouteKeyName() di model Barang mengembalikan 'id_barang' jika ingin binding by id_barang
        ->parameters(['barang' => 'barang'])
        ->except(['show']);
});

/* ===================== PELANGGAN ========================= */
Route::middleware('auth:pelanggan')->group(function () {
    Route::get('/home', [CustomerHomeController::class, 'index'])->name('customer.home');
    Route::get('/home/search', [CustomerHomeController::class, 'search'])->name('customer.search');

    // CART khusus pelanggan
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id_barang}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

/* ===================== PROFIL (opsional) ================= */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__ . '/auth.php'; // tetap dimatikan agar tidak bentrok dengan /login custom

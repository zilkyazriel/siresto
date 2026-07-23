<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\ComplaintController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Profil - semua role yang login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Menu - pemilik
Route::middleware(['auth', 'role:pemilik'])->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('menus.index');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menus.create');
    Route::post('/menu', [MenuController::class, 'store'])->name('menus.store');
    Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
    Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
});

// Kategori - pemilik
Route::middleware(['auth', 'role:pemilik'])->group(function () {
    Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// Meja - pelayan + pemilik
Route::middleware(['auth', 'role:pelayan,pemilik'])->group(function () {
    Route::get('/meja', [TableController::class, 'index'])->name('tables.index');
    Route::post('/meja', [TableController::class, 'store'])->name('tables.store');
    Route::put('/meja/{table}', [TableController::class, 'update'])->name('tables.update');
    Route::delete('/meja/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
    Route::get('/meja/denah', [TableController::class, 'denah'])->name('tables.denah');
    Route::post('/meja/{table}/bersih', [TableController::class, 'markClean'])->name('tables.markClean');
});

// Staf - pemilik
Route::middleware(['auth', 'role:pemilik'])->group(function () {
    Route::get('/staf', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staf', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/staf/{user}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staf/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');
});

// Supplier - gudang + pemilik
Route::middleware(['auth', 'role:gudang,pemilik'])->group(function () {
    Route::get('/supplier', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
});

// Laporan - pemilik
Route::middleware(['auth', 'role:pemilik'])->group(function () {
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');
});

// POS / buat pesanan - pelayan + pemilik
Route::middleware(['auth', 'role:pelayan,pemilik'])->group(function () {
    Route::get('/pesanan', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/pesanan', [OrderController::class, 'store'])->name('orders.store');
});

// Keluhan pelanggan (Pro-12) - pelayan + pemilik
Route::middleware(['auth', 'role:pelayan,pemilik'])->group(function () {
    Route::get('/keluhan', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/keluhan/buat', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/keluhan', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/keluhan/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/keluhan/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');
}); 

// Daftar & detail pesanan - pelayan, kasir, koki, pemilik
Route::middleware(['auth', 'role:pelayan,kasir,koki,pemilik'])->group(function () {
    Route::get('/pesanan/daftar', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// Update status pesanan (dapur / pelayan) - pelayan, koki, pemilik
Route::middleware(['auth', 'role:pelayan,koki,pemilik'])->group(function () {
    Route::post('/pesanan/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// Ubah / batal pesanan (Pro-11) - pelayan + pemilik
Route::middleware(['auth', 'role:pelayan,pemilik'])->group(function () {
    Route::get('/pesanan/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/pesanan/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Kasir - kasir + pemilik
Route::middleware(['auth', 'role:kasir,pemilik'])->group(function () {
    Route::get('/kasir', [CashierController::class, 'index'])->name('cashier.index');
    Route::get('/kasir/{order}/bayar', [CashierController::class, 'show'])->name('cashier.show');
    Route::post('/kasir/{order}/bayar', [CashierController::class, 'pay'])->name('cashier.pay');
    Route::get('/kasir/{order}/nota', [CashierController::class, 'receipt'])->name('cashier.receipt');
});

// Dapur / KDS - koki + pemilik
Route::middleware(['auth', 'role:koki,pemilik'])->group(function () {
    Route::get('/dapur', [KitchenController::class, 'index'])->name('kitchen.index');
});

// Gudang / Stok - gudang + pemilik
Route::middleware(['auth', 'role:gudang,pemilik'])->group(function () {
    Route::get('/stok', [StockController::class, 'index'])->name('stocks.index');
    Route::post('/stok', [StockController::class, 'store'])->name('stocks.store');
    Route::put('/stok/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::delete('/stok/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
});
// Barang Masuk (penerimaan bahan) - gudang + pemilik
Route::middleware(['auth', 'role:gudang,pemilik'])->group(function () {
    Route::get('/barang-masuk', [StockEntryController::class, 'index'])->name('stock-entries.index');
    Route::get('/barang-masuk/create', [StockEntryController::class, 'create'])->name('stock-entries.create');
    Route::post('/barang-masuk', [StockEntryController::class, 'store'])->name('stock-entries.store');
    Route::get('/barang-masuk/{stockEntry}', [StockEntryController::class, 'show'])->name('stock-entries.show');
});
require __DIR__.'/auth.php';

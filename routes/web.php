<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OrderController;




Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dapur', fn () => 'Halaman Dapur (khusus Koki)')
        ->middleware('role:koki,pemilik')->name('dapur.index');

    Route::get('/kasir', fn () => 'Halaman Kasir (khusus Kasir)')
        ->middleware('role:kasir,pemilik')->name('kasir.index');
});
Route::middleware('auth')->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('menus.index');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menus.create');
    Route::post('/menu', [MenuController::class, 'store'])->name('menus.store');
    Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
    Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
}); 
Route::middleware('auth')->group(function () {
    Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
}); 
Route::middleware('auth')->group(function () {
    Route::get('/meja', [TableController::class, 'index'])->name('tables.index');
    Route::post('/meja', [TableController::class, 'store'])->name('tables.store');
    Route::put('/meja/{table}', [TableController::class, 'update'])->name('tables.update');
    Route::delete('/meja/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
}); 
Route::middleware('auth')->group(function () {
    Route::get('/staf', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staf', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/staf/{user}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staf/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');
}); 

Route::middleware('auth')->group(function () {
    Route::get('/supplier', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
}); 
Route::middleware('auth')->group(function () {
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');
}); 
Route::middleware('auth')->group(function () {
    Route::get('/pesanan', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/pesanan', [OrderController::class, 'store'])->name('orders.store');
    // Sementara: biar menu "Pesanan" di sidebar langsung buka POS (nanti diganti Daftar Pesanan 3.3)
    Route::get('/pesanan/daftar', fn () => redirect()->route('orders.create'))->name('orders.index');
}); 
require __DIR__.'/auth.php';

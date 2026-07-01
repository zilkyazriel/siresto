<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

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
require __DIR__.'/auth.php';

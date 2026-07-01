<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
require __DIR__.'/auth.php';

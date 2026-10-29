<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Policies\LibraryPolicy;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('library'));
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::view('/library', 'library')->middleware(['auth', 'can:viewAny,'.LibraryPolicy::class])->name('library');

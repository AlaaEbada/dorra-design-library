<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\DesignListingController;
use App\Policies\LibraryPolicy;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('library'));
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/library', DesignListingController::class)->middleware(['auth', 'can:viewAny,'.LibraryPolicy::class])->name('library');
Route::get('/admin/designs', [DesignController::class, 'index'])->middleware('auth')->name('admin.designs');

Route::resource('designs', DesignController::class)->except('index')->middleware('auth');

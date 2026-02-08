<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', App\Http\Controllers\DashboardController::class)->name('login');

Auth::routes();


Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('products', App\Http\Controllers\ProductsController::class);
    route::resource('clients', ClientsController::class);
});
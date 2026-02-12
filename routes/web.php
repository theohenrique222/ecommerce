<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SellersController;
use Illuminate\Support\Facades\Route;

Route::get('/', App\Http\Controllers\DashboardController::class)->name('login');

Auth::routes();


Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('products', ProductsController::class);
    Route::resource('clients', ClientsController::class);
    Route::resource('sellers', SellersController::class);
    Route::resource('sales', SalesController::class);
    Route::post('sales/{sale}/payments', [SalesController::class, 'storePayment'])->name('sales.storePayment');
});
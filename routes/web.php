<?php

use App\Http\Controllers\Delivery\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('delivery.home');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return view('delivery.home');
    });


    Route::prefix('delivery')->group(function () {
        Route::get('/home', function () {
            return view('delivery.home');
        })->name('delivery.home');

        Route::prefix('products')->group(function () {
            Route::get('/{id}', [ProductController::class, 'show'])->name('delivery.products.show');
            Route::get('/', [ProductController::class, 'index'])->name('delivery.products');
            Route::post('/new_product', [ProductController::class, 'store'])->name('products.store');
            Route::post('/edit/{id}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');
        });


        Route::get('/orders', function () {
            return view('delivery.orders');
        })->name('delivery.orders');

        Route::get('/customers', function () {
            return view('delivery.customers');
        })->name('delivery.customers');

        Route::get('/payments', function () {
            return view('delivery.payments');
        })->name('delivery.payments');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Delivery\OrderController;
use App\Http\Controllers\Delivery\ProductController;
use App\Http\Controllers\Delivery\CustomerController;



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
            Route::get('/', [ProductController::class, 'index'])->name('delivery.products');
            Route::get('/show/{id}', [ProductController::class, 'show'])->name('products.show');
            Route::get('/showAll', [ProductController::class, 'showAll'])->name('products.showAll');
            Route::post('/new_product', [ProductController::class, 'store'])->name('products.store');
            Route::post('/edit/{id}', [ProductController::class, 'update'])->name('products.update');
        })->name('delivery.orders');


        Route::get('/orders', function () {
            return view('delivery.orders');
        })->name('delivery.orders');

        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('delivery.customers');
            Route::get('/show/{id}', [CustomerController::class, 'show'])->name('customers.show');
            Route::get('/showAll', [CustomerController::class, 'showAll'])->name('customers.showAll');
            Route::post('/new_customer', [CustomerController::class, 'store'])->name('customers.store');
            Route::post('/edit/{id}', [CustomerController::class, 'update'])->name('customers.update');
            Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])->name('customers.delete');
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('delivery.orders');
            Route::get('/show/{id}', [OrderController::class, 'show'])->name('orders.show');
            Route::get('/showAll', [OrderController::class, 'showAll'])->name('orders.showAll');
            Route::post('/new_customer', [OrderController::class, 'store'])->name('orders.store');
            Route::post('/edit/{id}', [OrderController::class, 'update'])->name('orders.update');
            Route::post('/import', [OrderController::class, 'import'])->name('orders.import');
            Route::delete('/delete/{id}', [OrderController::class, 'destroy'])->name('orders.delete');
        });

        Route::get('/payments', function () {
            return view('delivery.payments');
        })->name('delivery.payments');

        Route::get('/expenses', function () {
            return view('delivery.expenses');
        })->name('delivery.expenses');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

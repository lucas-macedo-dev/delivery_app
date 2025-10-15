<?php

use App\Http\Controllers\Delivery\ExpenseController;
use App\Http\Controllers\Delivery\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Delivery\OrderController;
use App\Http\Controllers\Delivery\ProductController;
use App\Http\Controllers\Delivery\CustomerController;
use App\Http\Controllers\Admin\UserApprovalController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/users/approve/{user}', [UserApprovalController::class, 'approve'])
        ->name('users.approve');
    Route::get('/users/reject/{user}', [UserApprovalController::class, 'reject'])
        ->name('users.reject');
});

Route::middleware(['auth', 'check.approved'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserApprovalController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/approve', [UserApprovalController::class, 'approveAction'])
        ->name('users.approve.action');
    Route::delete('/users/{user}/reject', [UserApprovalController::class, 'rejectAction'])
        ->name('users.reject.action');
    Route::patch('/users/{user}/revoke', [UserApprovalController::class, 'revokeAccess'])
        ->name('users.revoke');
});

Route::middleware(['auth', 'check.approved'])->group(function () {
    Route::get('/', (function () {
        return redirect()->route('delivery.home');
    }));
    Route::prefix('delivery')->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('delivery.home');

        Route::prefix('home')->group(function () {
            Route::get('/searchData', [HomeController::class, 'searchData'])->name('delivery.searchData');
        });

        Route::get('categories/showAll', [ProductController::class, 'loadCategories'])->name('categories.show');
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('delivery.products');
            Route::get('/show/{id}', [ProductController::class, 'show'])->name('products.show');
            Route::get('/showAll', [ProductController::class, 'showAll'])->name('products.showAll');
            Route::post('/new_product', [ProductController::class, 'store'])->name('products.store');
            Route::post('/edit/{id}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');
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
            Route::post('/new_order', [OrderController::class, 'store'])->name('orders.store');
            Route::post('/edit/{id}', [OrderController::class, 'update'])->name('orders.update');
            Route::post('/import', [OrderController::class, 'import'])->name('orders.import');
            Route::delete('/delete/{id}', [OrderController::class, 'destroy'])->name('orders.delete');
        });

        Route::get('/payments', function () {
            return view('delivery.payments');
        })->name('delivery.payments');

        Route::prefix('expenses')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('delivery.expenses');
            Route::get('/show/{id}', [ExpenseController::class, 'show'])->name('expenses.show');
            Route::get('/showAll', [ExpenseController::class, 'showAll'])->name('expenses.showAll');
            Route::post('/new_expense', [ExpenseController::class, 'store'])->name('expenses.store');
            Route::post('/edit/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
            Route::delete('/delete/{id}', [ExpenseController::class, 'destroy'])->name('expenses.delete');

            Route::get('/summary', [ExpenseController::class, 'summary']);
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Owner\AuthController as OwnerAuth;

use App\Http\Controllers\Admin\OperationController as AdminOperation;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuth::class, 'index'])->middleware('guest:admin')->name('login');
    Route::post('login', [AdminAuth::class, 'store'])->middleware('guest:admin')->name('login.store');
    Route::get('logout', [AdminAuth::class, 'destroy'])->middleware('auth:admin')->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

        Route::prefix('operation')->name('operation.')->group(function () {
            Route::get('/', [AdminOperation::class, 'index'])->name('login');
            Route::get('/create', [AdminOperation::class, 'create'])->name('create');
        });
    });
});

Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('login', [OwnerAuth::class, 'create'])->middleware('guest:owner')->name('login');
    Route::post('login', [OwnerAuth::class, 'store'])->middleware('guest:owner')->name('login.store');
    Route::post('logout', [OwnerAuth::class, 'destroy'])->middleware('auth:owner')->name('logout');

    Route::middleware('auth:owner')->group(function () {
        Route::get('/', fn () => view('owner.dashboard'))->name('dashboard');
    });
});


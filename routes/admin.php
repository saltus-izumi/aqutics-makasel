<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\OperationController as AdminOperation;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuth::class, 'index'])->middleware('guest:admin')->name('login');
    Route::post('login', [AdminAuth::class, 'store'])->middleware('guest:admin')->name('login.store');
    Route::get('logout', [AdminAuth::class, 'destroy'])->middleware('auth:admin')->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

        Route::prefix('operation')->name('operation.')->group(function () {
            Route::get('/', [AdminOperation::class, 'index'])->name('index');
            Route::get('/create', [AdminOperation::class, 'create'])->name('create');
            Route::post('/create', [AdminOperation::class, 'store'])->name('store');
            Route::get('/create/te/{teProgressId}', [AdminOperation::class, 'createTe'])->name('create.te');
            Route::get('/create/ge/{geProgressId}', [AdminOperation::class, 'createGe'])->name('create.ge');
            Route::get('/{operationId}', [AdminOperation::class, 'create'])->name('edit');

        });
    });
});

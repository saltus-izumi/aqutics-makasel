<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Owner\AuthController as OwnerAuth;
use App\Http\Controllers\Owner\OperationController as OwnerOperation;

Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('login', [OwnerAuth::class, 'create'])->middleware('guest:owner')->name('login');
    Route::post('login', [OwnerAuth::class, 'store'])->middleware('guest:owner')->name('login.store');
    Route::post('logout', [OwnerAuth::class, 'destroy'])->middleware('auth:owner')->name('logout');

    Route::middleware('auth:owner')->group(function () {
        Route::get('/', fn () => view('owner.dashboard'))->name('dashboard');

        Route::prefix('operation')->name('operation.')->group(function () {
            Route::get('/', [OwnerOperation::class, 'index'])->name('index');
            Route::get('/files/{operationFileId}', [OwnerOperation::class, 'previewFile'])->name('files.preview');
        });
    });
});

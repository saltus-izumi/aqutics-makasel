<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\OperationController as AdminOperation;
use App\Http\Controllers\Admin\GeProgressController as AdminGeProgress;
use App\Http\Controllers\Admin\EnProgressController as AdminEnProgress;
use App\Http\Controllers\Admin\ImportController as AdminImport;

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
            Route::get('/create/ge/{geProgressId}/{geProgressStep}', [AdminOperation::class, 'createGe'])->name('create.ge');
            Route::get('/{operationId}', [AdminOperation::class, 'create'])->name('edit');

        });

        Route::prefix('progress')->name('progress.')->group(function () {
            Route::prefix('ge')->name('ge.')->group(function () {
                Route::get('/', [AdminGeProgress::class, 'index'])->name('index');
                Route::get('/files/{geProgressFileId}', [AdminGeProgress::class, 'preview'])->name('preview');
                Route::get('/{geProgressId}', [AdminGeProgress::class, 'detail'])->name('detail');
                Route::get('/{geProgressId}/owner-settlement', [AdminGeProgress::class, 'ownerSettlement'])->name('owner-settlement');
            });
            Route::prefix('en')->name('en.')->group(function () {
                Route::get('/', [AdminEnProgress::class, 'index'])->name('index');
                Route::get('/files/{geProgressFileId}', [AdminEnProgress::class, 'preview'])->name('preview');
                Route::get('/{geProgressId}', [AdminEnProgress::class, 'detail'])->name('detail');
                Route::get('/{geProgressId}/owner-settlement', [AdminEnProgress::class, 'ownerSettlement'])->name('owner-settlement');
            });
        });

        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/import-procall', [AdminImport::class, 'importProcall'])->name('procall');
        });
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/import-personal-tenancy-application', [AdminImport::class, 'importPersonalTenancyApplication'])->name('personal-tenancy-application');
        });
    });
});

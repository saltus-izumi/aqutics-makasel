<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\OperationController as AdminOperation;
use App\Http\Controllers\Admin\Progress\GeController as AdminGe;
use App\Http\Controllers\Admin\Progress\EnController as AdminEn;
use App\Http\Controllers\Admin\Progress\TeController as AdminTe;
use App\Http\Controllers\Admin\ImportController as AdminImport;
use App\Http\Controllers\Admin\Master\OwnerController as AdminMasterOwner;
use App\Http\Controllers\Admin\Master\MailTemplateController as AdminMailTemplate;


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
            Route::get('/create/te/{teProgressId}/{progressStep}', [AdminOperation::class, 'createTe'])->name('create.te');
            Route::get('/create/ge/{geProgressId}/{progressStep}', [AdminOperation::class, 'createGe'])->name('create.ge');
            Route::get('/{operationId}', [AdminOperation::class, 'create'])->name('edit');

        });

        Route::prefix('progress')->name('progress.')->group(function () {
            Route::prefix('ge')->name('ge.')->group(function () {
                Route::get('/', [AdminGe::class, 'index'])->name('index');
                Route::get('/files/{geProgressFileId}', [AdminGe::class, 'preview'])->name('preview');
                Route::get('/{geProgressId}', [AdminGe::class, 'detail'])->name('detail');
                Route::get('/{geProgressId}/owner-settlement', [AdminGe::class, 'ownerSettlement'])->name('owner-settlement');
            });
            Route::prefix('en')->name('en.')->group(function () {
                Route::get('/', [AdminEn::class, 'index'])->name('index');
                Route::get('/{enProgressId}', [AdminEn::class, 'detail'])->name('detail');
                Route::get('/{enProgressId}/approval', [AdminEn::class, 'approval'])->name('approval');
            });
            Route::prefix('te')->name('te.')->group(function () {
                Route::get('/', [AdminTe::class, 'index'])->name('index');
                Route::get('/files/{teProgressFileId}', [AdminTe::class, 'preview'])->name('preview');
                Route::get('/{teProgressId}', [AdminTe::class, 'detail'])->name('detail');
            });
        });

        Route::prefix('master')->name('master.')->group(function () {
            Route::prefix('owner')->name('owner.')->group(function () {
                Route::get('/', [AdminMasterOwner::class, 'index'])->name('index');
            });
            Route::prefix('mail-template')->name('mail-template.')->group(function () {
                Route::get('/', [AdminMailTemplate::class, 'index'])->name('index');
            });


        });

        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/import-procall', [AdminImport::class, 'importProcall'])->name('procall');
            Route::get('/import-procall-update', [AdminImport::class, 'importProcallUpdate'])->name('procall-update');
            Route::get('/import-individual-tenancy-application', [AdminImport::class, 'importIndividualTenancyApplication'])->name('individual-tenancy-application');
            Route::get('/import-corporate-tenancy-application', [AdminImport::class, 'importCorporateTenancyApplication'])->name('corporate-tenancy-application');
            Route::get('/import-tenant', [AdminImport::class, 'importTenant'])->name('tenant');
        });
    });
});

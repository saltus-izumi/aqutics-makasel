<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/admin')->name('admin.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('login.index');

});


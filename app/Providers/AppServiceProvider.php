<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $prefix = env('APP_DIR', null); // ← ここをサブディレクトリ名に合わせる（例: live, makasel など）

        if ($prefix) {
            Livewire::setScriptRoute(function ($handle) use ($prefix) {
                return Route::get("/{$prefix}/livewire/livewire.js", $handle);
            });
        }

        // // ついでに update もサブディレクトリ配下に寄せたい場合（環境によって必要）
        // Livewire::setUpdateRoute(function ($handle) use ($prefix) {
        //     return Route::post("/{$prefix}/livewire/update", $handle);
        // });
    }
}

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
            // スクリプトルートをサブディレクトリに設定
            Livewire::setScriptRoute(function ($handle) use ($prefix) {
                return Route::get("/{$prefix}/livewire/livewire.js", $handle)
                    ->name("{$prefix}.livewire.js");
            });

            // updateルートをサブディレクトリに設定
            // ルート名を 'livewire.update' で終わるようにする必要がある
            Livewire::setUpdateRoute(function ($handle) use ($prefix) {
                return Route::post("/{$prefix}/livewire/update", $handle)
                    ->name("{$prefix}.livewire.update");
            });
        }
    }
}

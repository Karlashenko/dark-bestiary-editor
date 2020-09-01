<?php

namespace App\Providers;

use App\Presentable;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Collection::macro('present', function () {
            return $this->map(function (Presentable $presentable) {
                return $presentable->present();
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}

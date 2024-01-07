<?php

declare(strict_types=1);

namespace Targetforce\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Targetforce\Base\Services\ResolverService;

class ResolverProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('targetforce.resolver', function () {
            return new ResolverService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

<?php

namespace Targetforce\Base\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Targetforce\Base\Routes\ApiRoutes;
use Targetforce\Base\Routes\WebRoutes;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::mixin(new ApiRoutes());
        Route::mixin(new WebRoutes());
    }
}

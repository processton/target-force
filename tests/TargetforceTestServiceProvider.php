<?php

namespace Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Targetforce\Base\Facades\Targetforce;

class TargetforceTestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Targetforce::setCurrentWorkspaceIdResolver(function () {
            return 1;
        });

        Route::group(['prefix' => 'targetforce'], function () {
            Targetforce::webRoutes();
            Targetforce::publicWebRoutes();
            Targetforce::apiRoutes();
            Targetforce::publicApiRoutes();
        });
    }
}

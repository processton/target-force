<?php

namespace Targetforce\Base;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Targetforce\Base\Console\Commands\PostDispatchCommand;
use Targetforce\Base\Providers\EventServiceProvider;
use Targetforce\Base\Providers\FormServiceProvider;
use Targetforce\Base\Providers\ResolverProvider;
use Targetforce\Base\Providers\RouteServiceProvider;
use Targetforce\Base\Providers\TargetforceAppServiceProvider;
use Targetforce\Base\Services\Targetforce;

class TargetforceBaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('targetforce.php'),
            ], 'targetforce-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/targetforce'),
            ], 'targetforce-views');

            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/targetforce'),
            ], 'targetforce-lang');

            $this->publishes([
                __DIR__.'/../public' => public_path('vendor/targetforce'),
            ], 'targetforce-assets');

            $this->commands([
                PostDispatchCommand::class,
            ]);

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command(PostDispatchCommand::class)->everyMinute()->withoutOverlapping();
            });
        }

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'targetforce');
        $this->loadJsonTranslationsFrom(resource_path('lang/vendor/targetforce'));
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'targetforce');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Providers.
        $this->app->register(TargetforceAppServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(FormServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(ResolverProvider::class);

        // Facade.
        $this->app->bind('targetforce', static function (Application $app) {
            return $app->make(Targetforce::class);
        });

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'targetforce');
    }
}

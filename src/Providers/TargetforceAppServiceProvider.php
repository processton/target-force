<?php

declare(strict_types=1);

namespace Targetforce\Base\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Targetforce\Base\Interfaces\QuotaServiceInterface;
use Targetforce\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Targetforce\Base\Repositories\Campaigns\MySqlCampaignTenantRepository;
use Targetforce\Base\Repositories\Campaigns\PostgresCampaignTenantRepository;
use Targetforce\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Targetforce\Base\Repositories\Messages\MySqlMessageTenantRepository;
use Targetforce\Base\Repositories\Messages\PostgresMessageTenantRepository;
use Targetforce\Base\Repositories\Subscribers\MySqlSubscriberTenantRepository;
use Targetforce\Base\Repositories\Subscribers\PostgresSubscriberTenantRepository;
use Targetforce\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Targetforce\Base\Services\Helper;
use Targetforce\Base\Services\QuotaService;
use Targetforce\Base\Traits\ResolvesDatabaseDriver;

class TargetforceAppServiceProvider extends ServiceProvider
{
    use ResolvesDatabaseDriver;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Campaign repository.
        $this->app->bind(CampaignTenantRepositoryInterface::class, function (Application $app) {
            if ($this->usingPostgres()) {
                return $app->make(PostgresCampaignTenantRepository::class);
            }

            return $app->make(MySqlCampaignTenantRepository::class);
        });

        // Message repository.
        $this->app->bind(MessageTenantRepositoryInterface::class, function (Application $app) {
            if ($this->usingPostgres()) {
                return $app->make(PostgresMessageTenantRepository::class);
            }

            return $app->make(MySqlMessageTenantRepository::class);
        });

        // Subscriber repository.
        $this->app->bind(SubscriberTenantRepositoryInterface::class, function (Application $app) {
            if ($this->usingPostgres()) {
                return $app->make(PostgresSubscriberTenantRepository::class);
            }

            return $app->make(MySqlSubscriberTenantRepository::class);
        });

        $this->app->bind(QuotaServiceInterface::class, QuotaService::class);

        $this->app->singleton('targetforce.helper', function () {
            return new Helper();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}

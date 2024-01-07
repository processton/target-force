<?php

declare(strict_types=1);

namespace Targetforce\Base\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;

class Targetforce
{
    /** @var Application */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @throws BindingResolutionException
     */
    public function publicApiRoutes(): void
    {
        $this->app->make('router')->targetforcePublicApiRoutes();
    }
    
    /**
     * @throws BindingResolutionException
     */
    public function apiRoutes(): void
    {
        $this->app->make('router')->targetforceApiRoutes();
    }

    /**
     * @throws BindingResolutionException
     */
    public function publicWebRoutes(): void
    {
        $this->app->make('router')->targetforcePublicWebRoutes();
    }

    /**
     * @throws BindingResolutionException
     */
    public function webRoutes(): void
    {
        $this->app->make('router')->targetforceWebRoutes();
    }

    /**
     * @throws BindingResolutionException
     */
    public function setCurrentWorkspaceIdResolver(callable $resolver): void
    {
        $this->app->make('targetforce.resolver')->setCurrentWorkspaceIdResolver($resolver);
    }

    /**
     * @throws BindingResolutionException
     */
    public function currentWorkspaceId(): ?int
    {
        return $this->app->make('targetforce.resolver')->resolveCurrentWorkspaceId();
    }

    /**
     * @throws BindingResolutionException
     */
    public function setSidebarHtmlContentResolver(callable $resolver): void
    {
        $this->app->make('targetforce.resolver')->setSidebarHtmlContentResolver($resolver);
    }

    /**
     * @throws BindingResolutionException
     */
    public function sidebarHtmlContent(): ?string
    {
        return $this->app->make('targetforce.resolver')->resolveSidebarHtmlContent();
    }

    /**
     * @throws BindingResolutionException
     */
    public function setHeaderHtmlContentResolver(callable $resolver): void
    {
        $this->app->make('targetforce.resolver')->setHeaderHtmlContentResolver($resolver);
    }

    /**
     * @throws BindingResolutionException
     */
    public function headerHtmlContent(): ?string
    {
        return $this->app->make('targetforce.resolver')->resolveHeaderHtmlContent();
    }
}

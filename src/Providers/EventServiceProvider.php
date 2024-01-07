<?php

namespace Targetforce\Base\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Targetforce\Base\Events\MessageDispatchEvent;
use Targetforce\Base\Events\SubscriberAddedEvent;
use Targetforce\Base\Events\Webhooks\MailgunWebhookReceived;
use Targetforce\Base\Events\Webhooks\MailjetWebhookReceived;
use Targetforce\Base\Events\Webhooks\PostmarkWebhookReceived;
use Targetforce\Base\Events\Webhooks\SendgridWebhookReceived;
use Targetforce\Base\Events\Webhooks\SesWebhookReceived;
use Targetforce\Base\Events\Webhooks\PostalWebhookReceived;
use Targetforce\Base\Listeners\MessageDispatchHandler;
use Targetforce\Base\Listeners\Webhooks\HandleMailgunWebhook;
use Targetforce\Base\Listeners\Webhooks\HandleMailjetWebhook;
use Targetforce\Base\Listeners\Webhooks\HandlePostmarkWebhook;
use Targetforce\Base\Listeners\Webhooks\HandleSendgridWebhook;
use Targetforce\Base\Listeners\Webhooks\HandleSesWebhook;
use Targetforce\Base\Listeners\Webhooks\HandlePostalWebhook;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MailgunWebhookReceived::class => [
            HandleMailgunWebhook::class,
        ],
        MessageDispatchEvent::class => [
            MessageDispatchHandler::class,
        ],
        PostmarkWebhookReceived::class => [
            HandlePostmarkWebhook::class,
        ],
        SendgridWebhookReceived::class => [
            HandleSendgridWebhook::class,
        ],
        SesWebhookReceived::class => [
            HandleSesWebhook::class
        ],
        MailjetWebhookReceived::class => [
            HandleMailjetWebhook::class
        ],
        PostalWebhookReceived::class => [
            HandlePostalWebhook::class
        ],
        SubscriberAddedEvent::class => [
            // ...
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

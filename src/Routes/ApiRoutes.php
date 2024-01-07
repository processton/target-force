<?php

declare(strict_types=1);

namespace Targetforce\Base\Routes;

use Illuminate\Routing\Router;

class ApiRoutes
{
    public function targetforceApiRoutes(): callable
    {
        return function () {
            $this->name('targetforce.api.')->prefix('v1')->namespace('\Targetforce\Base\Http\Controllers\Api')->group(static function (Router $apiRouter) {
                $apiRouter->apiResource('posts', 'PostsController');
                $apiRouter->post('posts/{id}/send', 'PostDispatchController@send')->name('posts.send');
                $apiRouter->apiResource('subscribers', 'SubscribersController');
                $apiRouter->apiResource('tags', 'TagsController');

                $apiRouter->apiResource('subscribers.tags', 'SubscriberTagsController')
                    ->except(['show', 'update', 'destroy']);
                $apiRouter->put('subscribers/{subscriber}/tags', 'SubscriberTagsController@update')
                    ->name('subscribers.tags.update');
                $apiRouter->delete('subscribers/{subscriber}/tags', 'SubscriberTagsController@destroy')
                    ->name('subscribers.tags.destroy');

                $apiRouter->apiResource('tags.subscribers', 'TagSubscribersController')
                    ->except(['show', 'update', 'destroy']);
                $apiRouter->put('tags/{tag}/subscribers', 'TagSubscribersController@update')
                    ->name('tags.subscribers.update');
                $apiRouter->delete('tags/{tag}/subscribers', 'TagSubscribersController@destroy')
                    ->name('tags.subscribers.destroy');

                $apiRouter->apiResource('templates', 'TemplatesController');
            });
        };
    }

    public function targetforcePublicApiRoutes(): callable
    {
        return function () {
            $this->name('targetforce.api.webhooks.')->prefix('v1/webhooks')->namespace('\Targetforce\Base\Http\Controllers\Api\Webhooks')->group(static function (Router $webhookRouter) {
                $webhookRouter->post('aws', 'SesWebhooksController@handle')->name('aws');
                $webhookRouter->post('mailgun', 'MailgunWebhooksController@handle')->name('mailgun');
                $webhookRouter->post('postmark', 'PostmarkWebhooksController@handle')->name('postmark');
                $webhookRouter->post('sendgrid', 'SendgridWebhooksController@handle')->name('sendgrid');
                $webhookRouter->post('mailjet', 'MailjetWebhooksController@handle')->name('mailjet');
                $webhookRouter->post('postal', 'PostalWebhooksController@handle')->name('postal');
            });

            $this->get('v1/ping', '\Targetforce\Base\Http\Controllers\Api\PingController@index');
        };
    }
}

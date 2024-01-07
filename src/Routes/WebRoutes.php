<?php

declare(strict_types=1);

namespace Targetforce\Base\Routes;

use Illuminate\Routing\Router;

class WebRoutes
{
    public function targetforcePublicWebRoutes(): callable
    {
        return function () {
            $this->name('targetforce.')->namespace('\Targetforce\Base\Http\Controllers')->group(static function (
                Router $appRouter
            ) {
                // Subscriptions
                $appRouter->name('subscriptions.')->namespace('Subscriptions')->prefix('subscriptions')->group(static function (
                    Router $subscriptionController
                ) {
                    $subscriptionController->get('unsubscribe/{messageHash}', 'SubscriptionsController@unsubscribe')
                        ->name('unsubscribe');
                    $subscriptionController->get(
                        'subscribe/{messageHash}',
                        'SubscriptionsController@subscribe'
                    )->name('subscribe');
                    $subscriptionController->put(
                        'subscriptions/{messageHash}',
                        'SubscriptionsController@update'
                    )->name('update');
                });

                // Webview.
                $appRouter->name('webview.')->prefix('webview')->namespace('Webview')->group(static function (
                    Router $webviewRouter
                ) {
                    $webviewRouter->get('{messageHash}', 'WebviewController@show')->name('show');
                });
            });
        };
    }

    public function targetforceWebRoutes(): callable
    {
        return function () {
            $this->name('targetforce.')->namespace('\Targetforce\Base\Http\Controllers')->group(static function (
                Router $appRouter
            ) {

                // Dashboard.
                $appRouter->get('/', 'DashboardController@index')->name('dashboard');

                $appRouter->get('/forms', 'Forms\FormsController@index')->name('forms');

                // Posts.
                $appRouter->resource('posts', 'Posts\PostsController')->except(['show', 'destroy']);
                $appRouter->name('posts.')->prefix('posts')->namespace('Posts')->group(static function (
                    Router $postRouter
                ) {
                    $postRouter->get('sent', 'PostsController@sent')->name('sent');
                    $postRouter->get('{id}', 'PostsController@show')->name('show');
                    $postRouter->get('{id}/preview', 'PostsController@preview')->name('preview');
                    $postRouter->put('{id}/send', 'PostDispatchController@send')->name('send');
                    $postRouter->get('{id}/status', 'PostsController@status')->name('status');
                    $postRouter->post('{id}/test', 'PostTestController@handle')->name('test');

                    $postRouter->get(
                        '{id}/confirm-delete',
                        'PostDeleteController@confirm'
                    )->name('destroy.confirm');
                    $postRouter->delete('', 'PostDeleteController@destroy')->name('destroy');

                    $postRouter->get('{id}/duplicate', 'PostDuplicateController@duplicate')->name('duplicate');

                    $postRouter->get('{id}/confirm-cancel', 'PostCancellationController@confirm')->name('confirm-cancel');
                    $postRouter->post('{id}/cancel', 'PostCancellationController@cancel')->name('cancel');

                    $postRouter->get('{id}/report', 'PostReportsController@index')->name('reports.index');
                    $postRouter->get('{id}/report/recipients', 'PostReportsController@recipients')
                        ->name('reports.recipients');
                    $postRouter->get('{id}/report/opens', 'PostReportsController@opens')->name('reports.opens');
                    $postRouter->get(
                        '{id}/report/clicks',
                        'PostReportsController@clicks'
                    )->name('reports.clicks');
                    $postRouter->get('{id}/report/unsubscribes', 'PostReportsController@unsubscribes')
                        ->name('reports.unsubscribes');
                    $postRouter->get(
                        '{id}/report/bounces',
                        'PostReportsController@bounces'
                    )->name('reports.bounces');
                });

                // Messages.
                $appRouter->name('messages.')->prefix('messages')->group(static function (Router $messageRouter) {
                    $messageRouter->get('/', 'MessagesController@index')->name('index');
                    $messageRouter->get('draft', 'MessagesController@draft')->name('draft');
                    $messageRouter->get('{id}/show', 'MessagesController@show')->name('show');
                    $messageRouter->post('send', 'MessagesController@send')->name('send');
                    $messageRouter->delete('{id}/delete', 'MessagesController@delete')->name('delete');
                    $messageRouter->post('send-selected', 'MessagesController@sendSelected')->name('send-selected');
                });

                // Email Services.
                $appRouter->name('email_services.')->prefix('email-services')->namespace('EmailServices')->group(static function (
                    Router $servicesRouter
                ) {
                    $servicesRouter->get('/', 'EmailServicesController@index')->name('index');
                    $servicesRouter->get('create', 'EmailServicesController@create')->name('create');
                    $servicesRouter->get('type/{id}', 'EmailServicesController@emailServicesTypeAjax')->name('ajax');
                    $servicesRouter->post('/', 'EmailServicesController@store')->name('store');
                    $servicesRouter->get('{id}/edit', 'EmailServicesController@edit')->name('edit');
                    $servicesRouter->put('{id}', 'EmailServicesController@update')->name('update');
                    $servicesRouter->delete('{id}', 'EmailServicesController@delete')->name('delete');

                    $servicesRouter->get('{id}/test', 'TestEmailServiceController@create')->name('test.create');
                    $servicesRouter->post('{id}/test', 'TestEmailServiceController@store')->name('test.store');
                });

                // Tags.
                $appRouter->resource('tags', 'Tags\TagsController')->except(['show']);
                $appRouter->resource('templates', 'TemplatesController');

                // Subscribers.
                $appRouter->name('subscribers.')->prefix('subscribers')->namespace('Subscribers')->group(static function (
                    Router $subscriberRouter
                ) {
                    $subscriberRouter->get('export', 'SubscribersController@export')->name('export');
                    $subscriberRouter->get('import', 'SubscribersImportController@show')->name('import');
                    $subscriberRouter->post('import', 'SubscribersImportController@store')->name('import.store');
                });
                $appRouter->resource('subscribers', 'Subscribers\SubscribersController');

                // Templates.
                $appRouter->resource('templates', 'TemplatesController')->except(['show']);
            });
        };
    }
}

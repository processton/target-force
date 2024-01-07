<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Api\Webhooks;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Targetforce\Base\Events\Webhooks\MailjetWebhookReceived;
use Targetforce\Base\Http\Controllers\Controller;

class MailjetWebhooksController extends Controller
{
    public function handle(): Response
    {
        /** @var array $payload */
        $payload = json_decode(request()->getContent(), true);

        Log::info('Mailjet webhook received', $payload);

        event(new MailjetWebhookReceived($payload));

        return response('OK');
    }
}

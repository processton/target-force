<?php

namespace Targetforce\Base\Events\Webhooks;

class MailjetWebhookReceived
{
    /** @var array */
    public $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}

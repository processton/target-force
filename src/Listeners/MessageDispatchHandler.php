<?php

declare(strict_types=1);

namespace Targetforce\Base\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Targetforce\Base\Events\MessageDispatchEvent;
use Targetforce\Base\Services\Messages\DispatchMessage;

class MessageDispatchHandler implements ShouldQueue
{
    /** @var string */
    public $queue = 'targetforce-message-dispatch';

    /** @var DispatchMessage */
    protected $dispatchMessage;

    public function __construct(DispatchMessage $dispatchMessage)
    {
        $this->dispatchMessage = $dispatchMessage;
    }

    /**
     * @throws Exception
     */
    public function handle(MessageDispatchEvent $event): void
    {
        $this->dispatchMessage->handle($event->message);
    }
}

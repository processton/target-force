<?php

declare(strict_types=1);

namespace Targetforce\Base\Services\Messages;

use Exception;
use Illuminate\Support\Facades\Log;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Services\Content\MergeContentService;
use Targetforce\Base\Services\Content\MergeSubjectService;

class DispatchMessage
{
    /** @var ResolveEmailService */
    protected $resolveEmailService;

    /** @var RelayMessage */
    protected $relayMessage;

    /** @var MergeContentService */
    protected $mergeContentService;

    /** @var MergeSubjectService */
    protected $mergeSubjectService;

    /** @var MarkAsSent */
    protected $markAsSent;

    public function __construct(
        MergeContentService $mergeContentService,
        MergeSubjectService $mergeSubjectService,
        ResolveEmailService $resolveEmailService,
        RelayMessage $relayMessage,
        MarkAsSent $markAsSent
    ) {
        $this->mergeContentService = $mergeContentService;
        $this->mergeSubjectService = $mergeSubjectService;
        $this->resolveEmailService = $resolveEmailService;
        $this->relayMessage = $relayMessage;
        $this->markAsSent = $markAsSent;
    }

    /**
     * @throws Exception
     */
    public function handle(Message $message): ?string
    {
        if (!$this->isValidMessage($message)) {
            Log::info('Message is not valid, skipping id=' . $message->id);

            return null;
        }

        $message = $this->mergeSubject($message);

        $mergedContent = $this->getMergedContent($message);

        $emailService = $this->getEmailService($message);

        $trackingOptions = MessageTrackingOptions::fromMessage($message);

        $messageId = $this->dispatch($message, $emailService, $trackingOptions, $mergedContent);

        $this->markSent($message, $messageId);

        return $messageId;
    }

    /**
     * The message's subject is merged and persisted to the database
     * so that we have a permanent record of the merged tags at the
     * time of dispatch.
     */
    protected function mergeSubject(Message $message): Message
    {
        $message->subject = $this->mergeSubjectService->handle($message);
        $message->save();

        return $message;
    }

    /**
     * @throws Exception
     */
    protected function getMergedContent(Message $message): string
    {
        return $this->mergeContentService->handle($message);
    }

    /**
     * @throws Exception
     */
    protected function dispatch(Message $message, EmailService $emailService, MessageTrackingOptions $trackingOptions, string $mergedContent): ?string
    {
        $messageOptions = (new MessageOptions)
            ->setTo($message->recipient_email)
            ->setFromEmail($message->from_email)
            ->setFromName($message->from_name)
            ->setSubject($message->subject)
            ->setTrackingOptions($trackingOptions);

        $messageId = $this->relayMessage->handle($mergedContent, $messageOptions, $emailService);

        Log::info('Message has been dispatched.', ['message_id' => $messageId]);

        return $messageId;
    }

    /**
     * @throws Exception
     */
    protected function getEmailService(Message $message): EmailService
    {
        return $this->resolveEmailService->handle($message);
    }

    protected function markSent(Message $message, string $messageId): Message
    {
        return $this->markAsSent->handle($message, $messageId);
    }

    protected function isValidMessage(Message $message): bool
    {
        if ($message->sent_at) {
            return false;
        }

        if (!$message->isPost()) {
            return true;
        }

        $post = Post::find($message->source_id);

        if (!$post) {
            return false;
        }

        return $post->status_id !== PostStatus::STATUS_CANCELLED;
    }
}

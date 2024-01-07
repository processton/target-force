<?php

declare(strict_types=1);

namespace Targetforce\Base\Services\Messages;

use Exception;
use Illuminate\Support\Facades\Log;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;
use Targetforce\Base\Services\Content\MergeContentService;

class DispatchTestMessage
{
    /** @var ResolveEmailService */
    protected $resolveEmailService;

    /** @var RelayMessage */
    protected $relayMessage;

    /** @var MergeContentService */
    protected $mergeContent;

    /** @var PostTenantRepositoryInterface */
    protected $postTenant;

    public function __construct(
        PostTenantRepositoryInterface $postTenant,
        MergeContentService $mergeContent,
        ResolveEmailService $resolveEmailService,
        RelayMessage $relayMessage
    ) {
        $this->resolveEmailService = $resolveEmailService;
        $this->relayMessage = $relayMessage;
        $this->mergeContent = $mergeContent;
        $this->postTenant = $postTenant;
    }

    /**
     * @throws Exception
     */
    public function handle(int $workspaceId, int $postId, string $recipientEmail): ?string
    {
        $post = $this->resolvePost($workspaceId, $postId);

        if (!$post) {
            Log::error(
                'Unable to get post to send test message.',
                ['workspace_id' => $workspaceId, 'post_id' => $postId]
            );
            return null;
        }

        $message = $this->createTestMessage($post, $recipientEmail);

        $mergedContent = $this->getMergedContent($message);

        $emailService = $this->getEmailService($message);

        $trackingOptions = MessageTrackingOptions::fromPost($post);

        return $this->dispatch($message, $emailService, $trackingOptions, $mergedContent);
    }

    /**
     * @throws Exception
     */
    public function testService(int $workspaceId, EmailService $emailService, MessageOptions $options): ?string
    {
        $message = new Message([
            'workspace_id' => $workspaceId,
            'recipient_email' => $options->getTo(),
            'subject' => $options->getSubject(),
            'from_name' => 'Targetforce',
            'from_email' => $options->getFromEmail(),
            'hash' => 'abc123',
        ]);

        $trackingOptions = (new MessageTrackingOptions())->disable();

        return $this->dispatch($message, $emailService, $trackingOptions, $options->getBody());
    }

    /**
     * @throws Exception
     */
    protected function resolvePost(int $workspaceId, int $postId): ?Post
    {
        return $this->postTenant->find($workspaceId, $postId);
    }

    /**
     * @throws Exception
     */
    protected function getMergedContent(Message $message): string
    {
        return $this->mergeContent->handle($message);
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

    protected function createTestMessage(Post $post, string $recipientEmail): Message
    {
        return new Message([
            'workspace_id' => $post->workspace_id,
            'source_type' => Post::class,
            'source_id' => $post->id,
            'recipient_email' => $recipientEmail,
            'subject' => '[Test] ' . $post->subject,
            'from_name' => $post->from_name,
            'from_email' => $post->from_email,
            'hash' => 'abc123',
        ]);
    }
}

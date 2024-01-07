<?php

namespace Targetforce\Base\Services\Messages;

use Exception;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;
use Targetforce\Pro\Repositories\AutomationScheduleRepository;

class ResolveEmailService
{
    /** @var PostTenantRepositoryInterface */
    protected $postTenantRepository;

    public function __construct(PostTenantRepositoryInterface $postTenantRepository)
    {
        $this->postTenantRepository = $postTenantRepository;
    }

    /**
     * @throws Exception
     */
    public function handle(Message $message): EmailService
    {
        if ($message->isAutomation()) {
            return $this->resolveAutomationEmailService($message);
        }

        if ($message->isPost()) {
            return $this->resolvePostEmailService($message);
        }

        throw new Exception('Unable to resolve email service for message id=' . $message->id);
    }

    /**
     * Resolve the email service for an automation
     *
     * @param Message $message
     * @return EmailService
     * @throws Exception
     */
    protected function resolveAutomationEmailService(Message $message): EmailService
    {
        if (!$automationSchedule = app(AutomationScheduleRepository::class)->find(
            $message->source_id,
            ['automation_step.automation.email_service.type']
        )) {
            throw new Exception('Unable to resolve automation schedule for message id=' . $message->id);
        }

        if (!$emailService = $automationSchedule->automation_step->automation->email_service) {
            throw new Exception('Unable to resolve email service for message id=' . $message->id);
        }

        return $emailService;
    }

    /**
     * Resolve the provider for a post
     *
     * @param Message $message
     * @return EmailService
     * @throws Exception
     */
    protected function resolvePostEmailService(Message $message): EmailService
    {
        if (! $post = $this->postTenantRepository->find($message->workspace_id, $message->source_id, ['email_service'])) {
            throw new Exception('Unable to resolve post for message id=' . $message->id);
        }

        if (! $emailService = $post->email_service) {
            throw new Exception('Unable to resolve email service for message id=' . $message->id);
        }

        return $emailService;
    }
}

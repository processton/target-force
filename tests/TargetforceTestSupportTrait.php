<?php

declare(strict_types=1);

namespace Tests;

use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Models\Subscriber;
use Targetforce\Base\Models\Tag;

trait TargetforceTestSupportTrait
{
    protected function createEmailService(): EmailService
    {
        return EmailService::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);
    }

    protected function createPost(EmailService $emailService): Post
    {
        return Post::factory()
            ->withContent()
            ->sent()
            ->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
                'email_service_id' => $emailService->id,
            ]);
    }

    protected function createTag(): Tag
    {
        return Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);
    }

    protected function createSubscriber(): Subscriber
    {
        return Subscriber::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);
    }
}

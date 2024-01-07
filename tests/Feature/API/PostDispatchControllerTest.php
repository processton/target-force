<?php

declare(strict_types=1);

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Interfaces\QuotaServiceInterface;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Models\EmailServiceType;
use Targetforce\Base\Services\QuotaService;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostDispatchControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_draft_post_can_be_dispatched()
    {
        $this->ignoreQuota();

        $emailService = $this->createEmailService();

        $post = Post::factory()
            ->draft()
            ->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
                'email_service_id' => $emailService->id,
            ]);

        $this
            ->postJson(route('targetforce.api.posts.send', [
                'id' => $post->id
            ]))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'status_id' => PostStatus::STATUS_QUEUED,
                ],
            ]);
    }

    /** @test */
    public function a_sent_post_cannot_be_dispatched()
    {
        $this->ignoreQuota();

        $emailService = $this->createEmailService();

        $post = $this->createPost($emailService);

        $this
            ->postJson(route('targetforce.api.posts.send', [
                'id' => $post->id,
            ]))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'status_id' => 'The post must have a status of draft to be dispatched'
            ]);
    }

    /** @test */
    public function a_post_cannot_be_dispatched_if_the_number_of_subscribers_exceeds_the_ses_quota()
    {
        $this->instance(QuotaServiceInterface::class, Mockery::mock(QuotaService::class, function ($mock) {
            $mock->shouldReceive('exceedsQuota')->andReturn(true);
        }));

        $emailService = EmailService::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'type_id' => EmailServiceType::SES
        ]);

        $post = Post::factory()->draft()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'email_service_id' => $emailService->id,
        ]);

        $this
            ->postJson(route('targetforce.api.posts.send', [
                'id' => $post->id,
            ]))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The number of subscribers for this post exceeds your SES quota'
            ]);
    }

    protected function ignoreQuota(): void
    {
        $this->instance(QuotaServiceInterface::class, Mockery::mock(QuotaService::class, function ($mock) {
            $mock->shouldReceive('exceedsQuota')->andReturn(false);
        }));
    }
}

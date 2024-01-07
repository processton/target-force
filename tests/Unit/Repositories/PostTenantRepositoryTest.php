<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Models\Subscriber;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;
use Tests\TestCase;

class PostTenantRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var PostTenantRepositoryInterface */
    protected $postRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->postRepository = app(PostTenantRepositoryInterface::class);
    }

    /** @test */
    public function the_get_average_time_to_open_method_returns_the_average_time_taken_to_open_a_posts_message()
    {
        // given
        $emailService = $this->createEmailService();
        $post = $this->createPost($emailService);

        // 30 seconds
        $this->createOpenedMessages($post, 1, [
            'delivered_at' => now(),
            'opened_at' => now()->addSeconds(30),
        ]);

        // 60 seconds
        $this->createOpenedMessages($post, 1, [
            'delivered_at' => now(),
            'opened_at' => now()->addSeconds(60),
        ]);

        // when
        $averageTimeToOpen = $this->postRepository->getAverageTimeToOpen($post);

        // then
        // 45 seconds
        static::assertEquals('00:00:45', $averageTimeToOpen);
    }

    /** @test */
    public function the_get_average_time_to_open_method_returns_na_if_there_have_been_no_opens()
    {
        // given
        $emailService = $this->createEmailService();
        $post = $this->createPost($emailService);

        // when
        $averageTimeToOpen = $this->postRepository->getAverageTimeToOpen($post);

        // then
        static::assertEquals('N/A', $averageTimeToOpen);
    }

    /** @test */
    public function the_get_average_time_to_click_method_returns_the_average_time_taken_for_a_post_link_to_be_clicked_for_the_first_time()
    {
        // given
        $emailService = $this->createEmailService();
        $post = $this->createPost($emailService);

        // 30 seconds
        $this->createClickedMessage($post, 1, [
            'delivered_at' => now(),
            'clicked_at' => now()->addSeconds(30),
        ]);

        // 30 seconds
        $this->createClickedMessage($post, 1, [
            'delivered_at' => now(),
            'clicked_at' => now()->addSeconds(60),
        ]);

        // when
        $averageTimeToClick = $this->postRepository->getAverageTimeToClick($post);

        // then
        static::assertEquals('00:00:45', $averageTimeToClick);
    }

    /** @test */
    public function the_average_time_to_click_attribute_returns_na_if_there_have_been_no_clicks()
    {
        // given
        $emailService = $this->createEmailService();
        $post = $this->createPost($emailService);

        // when
        $averageTimeToClick = $this->postRepository->getAverageTimeToClick($post);

        // then
        static::assertEquals('N/A', $averageTimeToClick);
    }

    /** @test */
    public function the_cancel_post_method_sets_the_post_status_to_cancelled()
    {
        // given
        $post = Post::factory()->queued()->create();

        static::assertEquals(PostStatus::STATUS_QUEUED, $post->status_id);

        // when
        $success = $this->postRepository->cancelPost($post);

        // then
        static::assertTrue($success);
        static::assertEquals(PostStatus::STATUS_CANCELLED, $post->fresh()->status_id);
    }

    /** @test */
    public function the_cancel_post_method_deletes_draft_messages_if_the_post_has_any()
    {
        // given
        $emailService = $this->createEmailService();

        $post = Post::factory()->withContent()->sent()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'email_service_id' => $emailService->id,
            'save_as_draft' => 1,
        ]);

        $this->createPendingMessages($post, 1);

        static::assertCount(1, Message::all());

        // when
        $this->postRepository->cancelPost($post);

        // then
        static::assertCount(0, Message::all());
    }

    /** @test */
    public function the_cancel_post_method_does_not_delete_sent_messages()
    {
        // given
        $emailService = $this->createEmailService();

        $post = Post::factory()->withContent()->sent()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'email_service_id' => $emailService->id,
            'save_as_draft' => 1,
        ]);

        $this->createOpenedMessages($post, 1);

        static::assertCount(1, Message::all());

        // when
        $this->postRepository->cancelPost($post);

        // then
        static::assertCount(1, Message::all());
    }

    /** @test */
    public function the_get_count_method_returns_post_message_counts()
    {
        // given
        $emailService = $this->createEmailService();
        $post = $this->createPost($emailService);

        $expectedOpenedMessages = 1;
        $expectedUnopenedMessages = 2;
        $expectedClickedMessages = 3;
        $expectedBouncedMessages = 4;
        $expectedPendingMessages = 5;

        $this->createOpenedMessages($post, $expectedOpenedMessages);
        $this->createUnopenedMessages($post, $expectedUnopenedMessages);
        $this->createClickedMessages($post, $expectedClickedMessages);
        $this->createBouncedMessages($post, $expectedBouncedMessages);
        $this->createPendingMessages($post, $expectedPendingMessages);

        // when
        $counts = $this->postRepository->getCounts(collect($post->id), Targetforce::currentWorkspaceId());

        // then
        $totalSentCount = $expectedOpenedMessages
            + $expectedClickedMessages
            + $expectedUnopenedMessages
            + $expectedBouncedMessages;

        static::assertEquals($expectedOpenedMessages, $counts[$post->id]->opened);
        static::assertEquals($expectedClickedMessages, $counts[$post->id]->clicked);
        static::assertEquals($totalSentCount, $counts[$post->id]->sent);
        static::assertEquals($expectedBouncedMessages, $counts[$post->id]->bounced);
        static::assertEquals($expectedPendingMessages, $counts[$post->id]->pending);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createPost(EmailService $emailService): Post
    {
        return Post::factory()->withContent()->sent()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'email_service_id' => $emailService->id,
        ]);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createOpenedMessages(Post $post, int $quantity = 1, array $overrides = [])
    {
        $data = array_merge([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ]),
            'source_type' => Post::class,
            'source_id' => $post->id,
            'open_count' => 1,
            'sent_at' => now(),
            'delivered_at' => now(),
            'opened_at' => now(),
        ], $overrides);

        return Message::factory()->count($quantity)->create($data);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createUnopenedMessages(Post $post, int $count)
    {
        return Message::factory()->count($count)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ]),
            'source_type' => Post::class,
            'source_id' => $post->id,
            'open_count' => 0,
            'sent_at' => now(),
            'delivered_at' => now(),
        ]);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createClickedMessage(Post $post, int $quantity = 1, array $overrides = [])
    {
        $data = array_merge([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ]),
            'source_type' => Post::class,
            'source_id' => $post->id,
            'click_count' => 1,
            'sent_at' => now(),
            'delivered_at' => now(),
            'clicked_at' => now(),
        ], $overrides);

        return Message::factory()->count($quantity)->create($data);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createUnclickedMessage(Post $post, int $count)
    {
        return Message::factory()->count($count)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ]),
            'source_type' => Post::class,
            'source_id' => $post->id,
            'click_count' => 0,
            'sent_at' => now(),
            'delivered_at' => now(),
        ]);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createBouncedMessages(Post $post, int $count)
    {
        return Message::factory()->count($count)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ]),
            'source_type' => Post::class,
            'source_id' => $post->id,
            'sent_at' => now(),
            'bounced_at' => now(),
        ]);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createPendingMessages(Post $post, int $count)
    {
        return Message::factory()->count($count)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ]),
            'source_type' => Post::class,
            'source_id' => $post->id,
            'sent_at' => null,
        ]);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createClickedMessages(Post $post, int $quantity = 1, array $overrides = [])
    {
        $data = array_merge([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ]),
            'source_type' => Post::class,
            'source_id' => $post->id,
            'click_count' => 1,
            'sent_at' => now(),
            'delivered_at' => now(),
            'clicked_at' => now(),
        ], $overrides);

        return Message::factory()->count($quantity)->create($data);
    }
}

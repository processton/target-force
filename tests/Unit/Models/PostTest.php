<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Models\Subscriber;
use Targetforce\Base\Models\Tag;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_opens()
    {
        // given
        $emailService = $this->createEmailService();
        $post = $this->createPost($emailService);

        $openedMessages = $this->createOpenedMessage($post, 3);
        $this->createUnopenedMessage($post, 2);

        $opens = $post->opens;

        // then
        $opens->each(function ($open) use ($openedMessages) {
            $validMessages = $openedMessages->pluck('id')->toArray();

            static::assertContains($open->id, $validMessages);
        });

        static::assertEquals(3, $opens->count());
    }

    /** @test */
    public function the_unique_open_count_attribute_returns_the_number_of_unique_opens_for_a_post()
    {
        // given
        $emailService = $this->createEmailService();

        $post = Post::factory()->withContent()->sent()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'email_service_id' => $emailService->id,
        ]);

        $this->createOpenedMessage($post, 3);

        // then
        static::assertEquals(3, $post->unique_open_count);
    }

    /** @test */
    public function the_total_open_count_attribute_returns_the_total_number_of_opens_for_a_post()
    {
        // given
        $emailService = $this->createEmailService();

        $post = $this->createPost($emailService);

        $this->createOpenedMessage($post, 3, [
            'open_count' => 5
        ]);

        // then
        static::assertEquals(15, $post->total_open_count);
    }

    /** @test */
    public function it_has_many_clicks()
    {
        // given
        $emailService = $this->createEmailService();

        $post = $this->createPost($emailService);
        $clickedMessages = $this->createClickedMessage($post, 3);
        $this->createUnclickedMessage($post, 2);

        $clicks = $post->clicks;

        // then
        $clicks->each(function ($click) use ($clickedMessages) {
            $validMessages = $clickedMessages->pluck('id')->toArray();

            static::assertContains($click->id, $validMessages);
        });

        static::assertEquals(3, $clicks->count());
    }

    /** @test */
    public function the_unique_click_count_attribute_returns_the_number_of_unique_clicks_for_a_post()
    {
        // given
        $emailService = $this->createEmailService();

        $post = $this->createPost($emailService);

        $this->createClickedMessage($post, 3);

        // then
        static::assertEquals(3, $post->unique_click_count);
    }

    /** @test */
    public function the_total_click_count_attribute_returns_the_total_number_of_clicks_for_a_post()
    {
        // given
        $emailService = $this->createEmailService();

        $post = $this->createPost($emailService);

        $this->createClickedMessage($post, 3, [
            'click_count' => 5,
        ]);

        // then
        static::assertEquals(15, $post->total_click_count);
    }

    /** @test */
    public function the_cancelled_attribute_returns_true_if_the_post_is_cancelled()
    {
        // given
        $post = Post::factory()->cancelled()->create();

        // then
        static::assertTrue($post->cancelled);
    }

    /** @test */
    public function the_can_be_cancelled_method_returns_true_if_the_post_is_queued()
    {
        // given
        /** @var Post $post */
        $post = Post::factory()->queued()->create();

        // then
        static::assertTrue($post->canBeCancelled());
    }

    /** @test */
    public function the_can_be_cancelled_method_returns_true_if_the_post_is_sending()
    {
        // given
        /** @var Post $post */
        $post = Post::factory()->sending()->create();

        // then
        static::assertTrue($post->canBeCancelled());
    }

    /** @test */
    public function the_can_be_cancelled_method_returns_true_if_the_post_is_sent_and_saves_as_draft_and_not_all_drafts_have_been_sent()
    {
        // given
        $post = Post::factory()->sent()->create([
            'save_as_draft' => 1,
            'send_to_all' => 1,
        ]);

        // Subscribers
        Subscriber::factory()->count(5)->create([
            'workspace_id' => $post->workspace_id,
        ]);

        // Draft Messages
        Message::factory()->count(3)->pending()->create([
            'workspace_id' => $post->workspace_id,
            'source_id' => $post->id,
        ]);

        // Sent Messages
        Message::factory()->count(2)->dispatched()->create([
            'workspace_id' => $post->workspace_id,
            'source_id' => $post->id,
        ]);

        // then
        static::assertTrue($post->canBeCancelled());
    }

    /** @test */
    public function the_can_be_cancelled_method_returns_false_if_the_post_is_sent_and_saves_as_draft_and_all_drafts_have_been_sent()
    {
        // given
        $post = Post::factory()->sent()->create([
            'save_as_draft' => 1,
            'send_to_all' => 1,
        ]);

        $subscribers = Subscriber::factory()->count(5)->create([
            'workspace_id' => $post->workspace_id,
        ]);

        // Sent Messages
        Message::factory()->count($subscribers->count())->dispatched()->create([
            'workspace_id' => $post->workspace_id,
            'source_id' => $post->id,
        ]);

        // then
        static::assertFalse($post->canBeCancelled());
    }


    /** @test */
    public function the_all_drafts_created_method_returns_true_if_all_drafts_have_been_created()
    {
        // given
        $post = Post::factory()->sending()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'save_as_draft' => 1,
        ]);

        $tag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $post->tags()->attach($tag->id);

        $subscribers = Subscriber::factory()->count(5)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $tag->subscribers()->attach($subscribers->pluck('id'));

        // Message Drafts
        Message::factory()->count($subscribers->count())->pending()->create([
            'source_id' => $post->id,
        ]);

        // then
        static::assertTrue($post->allDraftsCreated());
    }

    /** @test */
    public function the_all_drafts_created_method_returns_false_if_all_drafts_have_not_been_created()
    {
        // given
        $post = Post::factory()->sending()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'save_as_draft' => 1,
        ]);

        $tag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $post->tags()->attach($tag->id);

        $subscribers = Subscriber::factory()->count(5)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $tag->subscribers()->attach($subscribers->pluck('id'));

        // Message Drafts
        Message::factory()->count(3)->pending()->create([
            'source_id' => $post->id,
        ]);

        // then
        static::assertFalse($post->allDraftsCreated());
    }

    /** @test */
    public function the_all_drafts_created_method_returns_true_if_the_post_does_not_save_as_draft()
    {
        // given
        $post = Post::factory()->sending()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'save_as_draft' => 0,
            'send_to_all' => 1,
        ]);

        Subscriber::factory()->count(5)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        // then
        static::assertTrue($post->allDraftsCreated());
    }

    protected function createOpenedMessage(Post $post, int $quantity = 1, array $overrides = [])
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

    protected function createUnopenedMessage(Post $post, int $count)
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
}

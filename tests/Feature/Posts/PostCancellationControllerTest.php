<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Models\Subscriber;
use Targetforce\Base\Models\Tag;
use Tests\TestCase;

class PostCancellationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_confirm_cancel_endpoint_returns_the_confirm_cancel_view()
    {
        $post = Post::factory()->queued()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $response = $this->get(route('targetforce.posts.confirm-cancel', ['id' => $post->id]));

        $response->assertViewIs('targetforce::posts.cancel');
    }

    /** @test */
    public function the_cancel_endpoint_cancels_a_queued_post()
    {
        $post = Post::factory()->queued()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertRedirect(route('targetforce.posts.index'));
        $response->assertSessionHas('success', 'The queued post was cancelled successfully.');
        static::assertEquals(PostStatus::STATUS_CANCELLED, $post->refresh()->status_id);
    }

    /** @test */
    public function the_cancel_endpoint_cancels_a_sending_post()
    {
        $post = Post::factory()->sending()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertRedirect(route('targetforce.posts.index'));
        static::assertEquals(PostStatus::STATUS_CANCELLED, $post->refresh()->status_id);
    }

    /** @test */
    public function the_cancel_endpoint_does_not_allow_a_draft_post_to_be_cancelled()
    {
        $post = Post::factory()->draft()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertRedirect(route('targetforce.posts.index'));
        $response->assertSessionHasErrors('postStatus', "{$post->status->name} posts cannot be cancelled.");
        static::assertEquals(PostStatus::STATUS_DRAFT, $post->refresh()->status_id);
    }

    /** @test */
    public function the_cancel_endpoint_does_not_allow_a_sent_post_to_be_cancelled()
    {
        $post = Post::factory()->sent()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertRedirect(route('targetforce.posts.index'));
        $response->assertSessionHasErrors('postStatus', "{$post->status->name} posts cannot be cancelled.");
        static::assertEquals(PostStatus::STATUS_SENT, $post->refresh()->status_id);
    }

    /** @test */
    public function the_cancel_endpoint_does_not_allow_a_cancelled_post_to_be_cancelled()
    {
        $post = Post::factory()->cancelled()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertRedirect(route('targetforce.posts.index'));
        $response->assertSessionHasErrors('postStatus', "{$post->status->name} posts cannot be cancelled.");
        static::assertEquals(PostStatus::STATUS_CANCELLED, $post->refresh()->status_id);
    }

    /** @test */
    public function when_a_sending_send_to_all_post_is_cancelled_the_user_is_told_how_many_messages_were_dispatched()
    {
        $post = Post::factory()->sending()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'save_as_draft' => 0,
            'send_to_all' => 1,
        ]);

        // Dispatched
        Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ])->id,
            'source_id' => $post->id,
            'sent_at' => now(),
        ]);

        // Not Sent
        Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
            ])->id,
            'source_id' => $post->id,
            'sent_at' => null,
        ]);

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertSessionHas('success', "The post was cancelled whilst being processed (~1/2 dispatched).");
    }

    /** @test */
    public function when_a_sending_not_send_to_all_post_is_cancelled_the_user_is_told_how_many_messages_were_dispatched()
    {
        $tag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);
        $post = Post::factory()->sending()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'save_as_draft' => 0,
            'send_to_all' => 0,
        ]);
        $post->tags()->attach($tag->id);

        // Dispatched
        $subscriber = Subscriber::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);
        $subscriber->tags()->attach($tag->id);
        Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => $subscriber->id,
            'source_id' => $post->id,
            'sent_at' => now(),
        ]);

        // Not Sent
        $otherSubscriber = Subscriber::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);
        $otherSubscriber->tags()->attach($tag->id);
        Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => $otherSubscriber->id,
            'source_id' => $post->id,
            'sent_at' => null,
        ]);

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertSessionHas('success', "The post was cancelled whilst being processed (~1/2 dispatched).");
    }

    /** @test */
    public function posts_that_save_as_draft_cannot_be_cancelled_until_every_draft_message_has_been_created()
    {
        $post = Post::factory()->sending()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'send_to_all' => 0,
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

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertSessionHasErrors(
            'messagesPendingDraft',
            'Posts that save draft messages cannot be cancelled until all drafts have been created.'
        );
    }

    /** @test */
    public function posts_that_save_as_draft_can_be_cancelled_if_every_draft_message_has_been_created()
    {
        $post = Post::factory()->sending()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'send_to_all' => 0,
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

        $response = $this->post(route('targetforce.posts.cancel', ['id' => $post->id]));

        $response->assertSessionHas(
            'success',
            'The post was cancelled and any remaining draft messages were deleted.'
        );
    }
}

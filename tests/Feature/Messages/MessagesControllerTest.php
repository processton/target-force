<?php

declare(strict_types=1);

namespace Tests\Feature\Messages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Message;
use Tests\TestCase;

class MessagesControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_index_of_sent_messages_is_accessible_to_an_authenticated_user()
    {
        // given
        Message::factory()->count(3)->create(['workspace_id' => Targetforce::currentWorkspaceId(), 'sent_at' => now()]);

        // when
        $response = $this->get(route('targetforce.messages.index'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function the_index_of_draft_messages_is_accessible_to_an_authenticated_user()
    {
        // given
        Message::factory()->count(3)->create(['workspace_id' => Targetforce::currentWorkspaceId(), 'sent_at' => null]);

        // when
        $response = $this->get(route('targetforce.messages.draft'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function a_draft_message_can_be_viewed_by_an_authenticated_user()
    {
        // given
        $post = Post::factory()->withContent()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $message = Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'source_id' => $post->id,
            'sent_at' => null
        ]);

        // when
        $response = $this->get(route('targetforce.messages.show', $message->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function a_draft_message_can_be_deleted()
    {
        // given
        $post = Post::factory()->withContent()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $message = Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'source_id' => $post->id,
            'sent_at' => null
        ]);

        // when
        $this->delete(route('targetforce.messages.delete', $message->id))
            ->assertRedirect(route('targetforce.messages.draft'));

        // then
        $this->assertDatabaseMissing('targetforce_messages', ['id' => $message->id]);
    }

    /** @test */
    public function a_sent_message_cannot_be_deleted()
    {
        // given
        $post = Post::factory()->withContent()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $message = Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'source_id' => $post->id,
            'sent_at' => now()
        ]);

        // when
        $this
            ->from(route('targetforce.messages.draft'))
            ->delete(route('targetforce.messages.delete', $message->id))
            ->assertRedirect(route('targetforce.messages.draft'));

        // then
        $this->assertDatabaseHas('targetforce_messages', ['id' => $message->id]);
    }

    /**
     * @test
     * https://github.com/mettle/targetforce/issues/90
     */
    public function a_message_can_be_sent_when_other_messages_have_been_sent()
    {
        // given
        $workspaceId = Targetforce::currentWorkspaceId();

        $post = Post::factory()->withContent()->create(['workspace_id' => $workspaceId]);

        Message::factory()->create([
            'workspace_id' => $workspaceId,
            'source_id' => $post->id,
            'sent_at' => now(), // Message already sent.
        ]);

        $draftMessage = Message::factory()->create([
            'workspace_id' => $workspaceId,
            'source_id' => $post->id,
            'queued_at' => now(),
        ]);

        // when
        $this->post(route('targetforce.messages.send'), ['id' => $draftMessage->id])
            ->assertRedirect(route('targetforce.messages.draft'))
            ->assertSessionHas('success');

        $draftMessage->refresh();

        // then
        self::assertNotNull($draftMessage->sent_at);
    }
}

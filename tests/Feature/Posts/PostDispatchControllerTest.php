<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Tag;
use Tests\TestCase;

class PostDispatchControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function posts_can_be_dispatched_to_tags_belonging_to_the_users_workspace()
    {
        // given
        $post = Post::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $validTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        // when
        $response = $this->put(route('targetforce.posts.send', $post->id), [
            'recipients' => 'send_to_tags',
            'tags' => [$validTag->id],
        ]);

        // then
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function posts_cannot_be_dispatched_to_tags_belonging_to_another_workspace()
    {
        // given
        $post = Post::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $validTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $invalidTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId() + 1,
        ]);

        // when
        $response = $this->put(route('targetforce.posts.send', $post->id), [
            'recipients' => 'send_to_tags',
            'tags' => [$validTag->id, $invalidTag->id],
        ]);

        // then
        $response->assertSessionHasErrors([
            'tags' => 'One or more of the tags is invalid.',
        ]);
    }
}

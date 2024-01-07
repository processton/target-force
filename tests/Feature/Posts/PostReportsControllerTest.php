<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Tests\TestCase;

class PostReportsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_sent_post_report_is_accessible_by_authenticated_users()
    {
        // given
        $post = $this->getPost();

        // when
        $response = $this->get(route('targetforce.posts.reports.index', $post->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function sent_post_recipients_are_accessible_by_authenticated_users()
    {
        // given
        $post = $this->getPost();

        // when
        $response = $this->get(route('targetforce.posts.reports.recipients', $post->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function sent_post_opens_are_accessible_by_authenticated_users()
    {
        // given
        $post = $this->getPost();

        // when
        $response = $this->get(route('targetforce.posts.reports.opens', $post->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function sent_post_clicks_are_accessible_by_authenticated_users()
    {
        // given
        $post = $this->getPost();

        // when
        $response = $this->get(route('targetforce.posts.reports.clicks', $post->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function sent_post_bounces_are_accessible_by_authenticated_users()
    {
        // given
        $post = $this->getPost();

        // when
        $response = $this->get(route('targetforce.posts.reports.bounces', $post->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function sent_post_unsubscribes_are_accessible_by_authenticated_users()
    {
        // given
        $post = $this->getPost();

        // when
        $response = $this->get(route('targetforce.posts.reports.unsubscribes', $post->id));

        // then
        $response->assertOk();
    }

    private function getPost(): Post
    {
        return Post::factory()
            ->sent()
            ->create(['workspace_id' => Targetforce::currentWorkspaceId()]);
    }
}

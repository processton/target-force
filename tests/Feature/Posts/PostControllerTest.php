<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Models\Template;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function the_index_of_posts_is_accessible_to_authenticated_users()
    {
        // given
        Post::factory()->count(3)->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        // when
        $response = $this->get(route('targetforce.posts.index'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function draft_posts_appear_on_the_draft_index()
    {
        $statuses = [
            PostStatus::STATUS_DRAFT,
            PostStatus::STATUS_QUEUED,
            PostStatus::STATUS_SENDING,
        ];

        foreach ($statuses as $status) {
            $post = Post::factory()->create(
                [
                    'workspace_id' => Targetforce::currentWorkspaceId(),
                    'status_id' => $status,
                ]
            );

            $this
                ->get(route('targetforce.posts.index'))
                ->assertSee($post->name);
        }
    }

    /** @test */
    public function sent_posts_dont_appear_on_the_draft_index()
    {
        $statuses = [
            PostStatus::STATUS_SENT,
            PostStatus::STATUS_CANCELLED,
        ];

        foreach ($statuses as $status) {
            $post = Post::factory()->create(
                [
                    'workspace_id' => Targetforce::currentWorkspaceId(),
                    'status_id' => $status,
                ]
            );

            $this
                ->get(route('targetforce.posts.index'))
                ->assertDontSee($post->name);
        }
    }

    /** @test */
    public function the_sent_index_of_posts_is_accessible_to_authenticated_users()
    {
        // given
        Post::factory()->count(3)->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        // when
        $response = $this->get(route('targetforce.posts.sent'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function sent_posts_appear_on_the_sent_index()
    {
        $statuses = [
            PostStatus::STATUS_SENT,
            PostStatus::STATUS_CANCELLED,
        ];

        foreach ($statuses as $status) {
            $post = Post::factory()->create(
                [
                    'workspace_id' => Targetforce::currentWorkspaceId(),
                    'status_id' => $status,
                ]
            );

            $this
                ->get(route('targetforce.posts.sent'))
                ->assertSee($post->name);
        }
    }

    /** @test */
    public function draft_posts_dont_appear_on_the_sent_index()
    {
        $statuses = [
            PostStatus::STATUS_DRAFT,
            PostStatus::STATUS_QUEUED,
            PostStatus::STATUS_SENDING,
        ];

        foreach ($statuses as $status) {
            $post = Post::factory()->create(
                [
                    'workspace_id' => Targetforce::currentWorkspaceId(),
                    'status_id' => $status,
                ]
            );

            $this
                ->get(route('targetforce.posts.sent'))
                ->assertDontSee($post->name);
        }
    }

    /** @test */
    public function the_post_creation_form_is_accessible_to_authenticated_users()
    {
        // when
        $response = $this->get(route('targetforce.posts.create'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function new_posts_can_be_created_by_authenticated_users()
    {
        $postStoreData = $this->generatePostStoreData();

        // when
        $response = $this->post(route('targetforce.posts.store'), $postStoreData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('targetforce_posts', [
            'name' => $postStoreData['name'],
        ]);
    }

    /** @test */
    public function the_preview_view_is_accessible_by_authenticated_users()
    {
        // given
        $post = Post::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        // when
        $response = $this->get(route('targetforce.posts.preview', $post->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function the_edit_view_is_accessible_by_authenticated_users()
    {
        // given
        $post = Post::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        // when
        $response = $this->get(route('targetforce.posts.edit', $post->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function a_post_is_updateable_by_authenticated_users()
    {
        // given
        $post = Post::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $postUpdateData = [
            'name' => $this->faker->word,
            'subject' => $this->faker->sentence,
            'from_name' => $this->faker->name,
            'from_email' => $this->faker->safeEmail,
            'email_service_id' => $post->email_service_id,
            'template_id' => $post->template_id,
            'content' => $this->faker->paragraph
        ];

        // when
        $response = $this->put(route('targetforce.posts.update', $post->id), $postUpdateData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('targetforce_posts', [
            'id' => $post->id,
            'name' => $postUpdateData['name'],
            'subject' => $postUpdateData['subject']
        ]);
    }

    /** @test */
    public function posts_can_be_set_to_not_track_opens()
    {
        // given
        $postStoreData = $this->generatePostStoreData();

        // when
        $response = $this->post(route('targetforce.posts.store'), $postStoreData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('targetforce_posts', [
            'name' => $postStoreData['name'],
            'is_open_tracking' => 0
        ]);
    }

    /** @test */
    public function posts_can_be_set_to_track_opens()
    {
        // given
        $postStoreData = $this->generatePostStoreData() + ['is_open_tracking' => true];

        // when
        $response = $this->post(route('targetforce.posts.store'), $postStoreData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('targetforce_posts', [
            'name' => $postStoreData['name'],
            'is_open_tracking' => 1
        ]);
    }

    /** @test */
    public function posts_can_be_set_to_not_track_clicks()
    {
        // given
        $postStoreData = $this->generatePostStoreData();

        // when
        $response = $this->post(route('targetforce.posts.store'), $postStoreData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('targetforce_posts', [
            'name' => $postStoreData['name'],
            'is_click_tracking' => 0
        ]);
    }

    /** @test */
    public function posts_can_be_set_to_track_clicks()
    {
        // given
        $postStoreData = $this->generatePostStoreData() + ['is_click_tracking' => true];

        // when
        $response = $this->post(route('targetforce.posts.store'), $postStoreData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('targetforce_posts', [
            'name' => $postStoreData['name'],
            'is_click_tracking' => 1
        ]);
    }

    /** @test */
    public function post_content_is_required_if_no_template_is_selected()
    {
        // given
        $postStoreData = $this->generatePostStoreData([
            'template_id' => null,
            'content' => null,
        ]);

        // when
        $response = $this->post(route('targetforce.posts.store'), $postStoreData);

        // then
        $response->assertSessionHasErrors('content');
    }

    /** @test */
    public function post_content_is_not_required_if_a_template_is_selected()
    {
        // given
        $postStoreData = $this->generatePostStoreData([
            'content' => null,
        ]);

        // when
        $response = $this->post(route('targetforce.posts.store'), $postStoreData);

        // then
        $response->assertSessionHasNoErrors();
    }

    private function generatePostStoreData(array $overrides = []): array
    {
        $emailService = EmailService::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);
        $template = Template::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        return array_merge([
            'name' => $this->faker->word,
            'subject' => $this->faker->sentence,
            'from_name' => $this->faker->name,
            'from_email' => $this->faker->safeEmail,
            'email_service_id' => $emailService->id,
            'template_id' => $template->id,
            'content' => $this->faker->paragraph
        ], $overrides);
    }
}

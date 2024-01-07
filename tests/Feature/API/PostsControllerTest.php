<?php

declare(strict_types=1);

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostsControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function a_list_of_a_workspaces_posts_can_be_retrieved()
    {
        $emailService = $this->createEmailService();

        $post = $this->createPost($emailService);

        $this
            ->getJson(route('targetforce.api.posts.index'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    Arr::only($post->toArray(), ['name'])
                ]
            ]);
    }

    /** @test */
    public function a_single_post_can_be_retrieved()
    {
        $emailService = $this->createEmailService();

        $post = $this->createPost($emailService);

        $this
            ->getJson(route('targetforce.api.posts.show', [
                'post' => $post->id,
            ]))
            ->assertOk()
            ->assertJson([
                'data' => Arr::only($post->toArray(), ['name']),
            ]);
    }

    /** @test */
    public function a_new_post_can_be_added()
    {
        $emailService = $this->createEmailService();

        $request = [
            'name' => $this->faker->colorName,
            'subject' => $this->faker->word,
            'from_name' => $this->faker->word,
            'from_email' => $this->faker->safeEmail,
            'email_service_id' => $emailService->id,
            'content' => $this->faker->sentence,
            'send_to_all' => 1,
            'scheduled_at' => now(),
        ];

        $this
            ->postJson(
                route('targetforce.api.posts.store'),
                $request
            )
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(['data' => $request]);

        $this->assertDatabaseHas('targetforce_posts', $request);
    }

    /** @test */
    public function a_post_can_be_updated()
    {
        $emailService = $this->createEmailService();

        $post = Post::factory()->draft()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'email_service_id' => $emailService->id,
        ]);

        $request = [
            'name' => $this->faker->word,
            'subject' => $this->faker->word,
            'from_name' => $this->faker->word,
            'from_email' => $this->faker->safeEmail,
            'email_service_id' => $emailService->id,
            'content' => $this->faker->sentence,
            'send_to_all' => 1,
            'scheduled_at' => now(),
        ];

        $this
            ->putJson(route('targetforce.api.posts.update', [
                'post' => $post->id,
            ]), $request)
            ->assertOk()
            ->assertJson(['data' => $request]);

        $this->assertDatabaseMissing('targetforce_posts', $post->toArray());
        $this->assertDatabaseHas('targetforce_posts', $request);
    }

    /** @test */
    public function a_sent_post_cannot_be_updated()
    {
        $emailService = $this->createEmailService();

        $post = $this->createPost($emailService);

        $request = [
            'name' => $this->faker->word,
            'subject' => $this->faker->word,
            'from_name' => $this->faker->word,
            'from_email' => $this->faker->safeEmail,
            'email_service_id' => $emailService->id,
            'content' => $this->faker->sentence,
            'send_to_all' => 1,
            'scheduled_at' => now(),
        ];

        $this
            ->putJson(route('targetforce.api.posts.update', [
                'post' => $post->id,
            ]), $request)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'status_id' => 'A post cannot be updated if its status is not draft'
            ]);

        $this->assertDatabaseMissing('targetforce_posts', $request);
        self::assertEquals($post->updated_at, $post->fresh()->updated_at);
    }
}

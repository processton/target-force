<?php

declare(strict_types=1);

namespace Tests\Feature\Subscribers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Subscriber;
use Targetforce\Base\Models\Tag;
use Tests\TestCase;

class SubscribersControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function new_subscribers_can_be_created_by_authenticated_users()
    {
        // given
        $subscriberStoreData = [
            'email' => $this->faker->email,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName
        ];

        // when
        $response = $this->post(route('targetforce.subscribers.store'), $subscriberStoreData);

        // then
        $response->assertRedirect();

        $this->assertDatabaseHas('targetforce_subscribers', [
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'email' => $subscriberStoreData['email']
        ]);
    }

    /** @test */
    public function the_edit_view_is_accessible_by_authenticated_users()
    {
        // given
        $subscriber = Subscriber::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        // when
        $response = $this->get(route('targetforce.subscribers.edit', $subscriber->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function a_subscriber_is_updateable_by_an_authenticated_user()
    {
        // given
        $subscriber = Subscriber::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $subscriberUpdateData = [
            'email' => $this->faker->email,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName
        ];

        // when
        $response = $this
            ->put(route('targetforce.subscribers.update', $subscriber->id), $subscriberUpdateData);

        // then
        $response->assertRedirect();

        $this->assertDatabaseHas('targetforce_subscribers', [
            'id' => $subscriber->id,
            'email' => $subscriberUpdateData['email'],
            'first_name' => $subscriberUpdateData['first_name'],
            'last_name' => $subscriberUpdateData['last_name'],
        ]);
    }

    /** @test */
    public function the_show_view_is_accessible_by_an_authenticated_user()
    {
        // given
        $subscriber = Subscriber::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        // when
        $response = $this->get(route('targetforce.subscribers.show', $subscriber->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function the_subscribers_index_lists_subscribers()
    {
        // given
        $subscriber = Subscriber::factory()->count(5)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        // when
        $response = $this->get(route('targetforce.subscribers.index'));

        // then
        $subscriber->each(static function (Subscriber $subscriber) use ($response) {
            $response->assertSee($subscriber->email);
            $response->assertSee("{$subscriber->first_name} {$subscriber->last_name}");
        });
    }

    /** @test */
    public function the_subscribers_index_can_be_filtered_by_tags()
    {
        // given
        $firstTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $secondTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $thirdTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $firstTagSubscriber = Subscriber::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $secondTagSubscriber = Subscriber::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $thirdTagSubscriber = Subscriber::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $firstTag->subscribers()->attach($firstTagSubscriber->id);
        $secondTag->subscribers()->attach($secondTagSubscriber->id);
        $thirdTag->subscribers()->attach($thirdTagSubscriber->id);

        // when
        $response = $this->get(route('targetforce.subscribers.index', [
            'tags' => [$firstTag->id, $secondTag->id]
        ]));

        // then
        $response->assertSee($firstTagSubscriber->email);
        $response->assertSee("{$firstTagSubscriber->first_name} {$firstTagSubscriber->last_name}");
        $response->assertSee($secondTagSubscriber->email);
        $response->assertSee("{$secondTagSubscriber->first_name} {$secondTagSubscriber->last_name}");
        $response->assertDontSee($thirdTagSubscriber->email);
        $response->assertDontSee("{$thirdTagSubscriber->first_name} {$thirdTagSubscriber->last_name}");
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Campaigns;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Campaign;
use Targetforce\Base\Models\Tag;
use Tests\TestCase;

class CampaignDispatchControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function campaigns_can_be_dispatched_to_tags_belonging_to_the_users_workspace()
    {
        // given
        $campaign = Campaign::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $validTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        // when
        $response = $this->put(route('targetforce.campaigns.send', $campaign->id), [
            'recipients' => 'send_to_tags',
            'tags' => [$validTag->id],
        ]);

        // then
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function campaigns_cannot_be_dispatched_to_tags_belonging_to_another_workspace()
    {
        // given
        $campaign = Campaign::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $validTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
        ]);

        $invalidTag = Tag::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId() + 1,
        ]);

        // when
        $response = $this->put(route('targetforce.campaigns.send', $campaign->id), [
            'recipients' => 'send_to_tags',
            'tags' => [$validTag->id, $invalidTag->id],
        ]);

        // then
        $response->assertSessionHasErrors([
            'tags' => 'One or more of the tags is invalid.',
        ]);
    }
}

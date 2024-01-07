<?php

declare(strict_types=1);

namespace Tests\Feature\Webview;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Campaign;
use Targetforce\Base\Models\Message;
use Tests\TestCase;

class WebviewControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_message_can_be_seen_in_the_webview()
    {
        // given
        $campaign = Campaign::factory()->withContent()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);
        $message = Message::factory()->create(['source_id' => $campaign->id, 'workspace_id' => Targetforce::currentWorkspaceId()]);

        // when
        $response = $this->get(route('targetforce.webview.show', $message->hash));

        // then
        $response->assertOk();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Templates;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Campaign;
use Targetforce\Base\Models\Template;
use Tests\TestCase;

class TemplatesControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function a_logged_in_user_can_see_template_index()
    {
        // when
        $response = $this->get(route('targetforce.templates.index'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function the_index_lists_existing_templates()
    {
        // given
        $template = Template::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        // when
        $response = $this->get(route('targetforce.templates.index'));

        // then
        $response->assertOk();
        $response->assertSee($template->name);
    }

    /** @test */
    public function a_logged_in_user_can_see_the_create_form()
    {
        // when
        $response = $this->get(route('targetforce.templates.create'));

        // then
        $response->assertOk();
        $response->assertSee('New Template');
        $response->assertSee('Template Name');
        $response->assertSee('Content');
    }

    /** @test */
    public function a_logged_in_user_can_store_a_new_template()
    {
        // given
        $data = [
            'name' => $this->faker->name,
            'content' => $this->faker->sentence
        ];

        // when
        $response = $this->post(route('targetforce.templates.store'), $data);

        // then
        $response->assertRedirect(route('targetforce.templates.index'));

        $this->assertDatabaseHas('targetforce_templates', [
            'name' => $data['name'],
            'content' => $data['content'],
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);
    }

    /** @test */
    public function storing_is_validated()
    {
        // given
        $namePostData = [
            'name' => $this->faker->name,
        ];

        $contentPostData = [
            'content' => $this->faker->sentence
        ];

        // when
        $namePostResponse = $this->post(route('targetforce.templates.store'), $namePostData);

        // then
        $namePostResponse->assertSessionHasErrors('content');

        // when
        $contentPostResponse = $this->post(route('targetforce.templates.store'), $contentPostData);

        // then
        $contentPostResponse->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_logged_in_user_can_see_the_edit_form()
    {
        // given
        $template = Template::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        // when
        $response = $this->get(route('targetforce.templates.edit', $template->id));

        // then
        $response->assertOk();

        $response->assertSee($template->name);
        $response->assertSee($template->content);
    }

    /** @test */
    public function a_logged_in_user_can_update_a_template()
    {
        // given
        $template = Template::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        $data = [
            'name' => $this->faker->name,
            'content' => $this->faker->sentence
        ];

        // when
        $response = $this->put(route('targetforce.templates.update', $template->id), $data);

        // then
        $response->assertRedirect(route('targetforce.templates.index'));

        $this->assertDatabaseMissing('targetforce_templates', [
            'id' => $template->id,
            'name' => $template->name,
            'content' => $template->content
        ]);

        $this->assertDatabaseHas('targetforce_templates', $data + ['id' => $template->id, 'workspace_id' => Targetforce::currentWorkspaceId()]);
    }

    /** @test */
    public function updates_are_validated()
    {
        // given
        $template = Template::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        $namePostData = [
            'name' => $this->faker->name,
        ];

        $contentPostData = [
            'content' => $this->faker->sentence
        ];

        // when
        $namePostResponse = $this->put(route('targetforce.templates.update', $template->id), $namePostData);
        
        // then
        $namePostResponse->assertSessionHasErrors('content');

        // when
        $contentPostResponse = $this->put(route('targetforce.templates.update', $template->id), $contentPostData);

        // then
        $contentPostResponse->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_logged_in_user_can_delete_a_template()
    {
        // given
        $template = Template::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        // when
        $response = $this->delete(route('targetforce.templates.destroy', $template->id));

        // then
        $response->assertRedirect(route('targetforce.templates.index'));

        $this->assertDatabaseMissing('targetforce_templates', [
            'id' => $template->id,
            'name' => $template->name
        ]);
    }

    /** @test */
    public function a_logged_in_user_cannot_delete_a_template_if_it_is_used()
    {
        // given
        $template = Template::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        Campaign::factory()->create([
            'template_id' => $template->id
        ]);

        // when
        $response = $this->from(route('targetforce.templates.index'))
            ->delete(route('targetforce.templates.destroy', $template->id));

        // then
        $response->assertRedirect(route('targetforce.templates.index'))
            ->assertSessionHasErrors(['template']);

        $this->assertDatabaseHas('targetforce_templates', [
            'id' => $template->id,
            'name' => $template->name
        ]);
    }

    /** @test */
    public function a_template_name_must_be_unique_for_a_workspace()
    {
        // given
        $template = Template::factory()->create(['workspace_id' => Targetforce::currentWorkspaceId()]);

        $request = [
            'name' => $template->name,
        ];

        // when
        $response = $this->post(route('targetforce.templates.store'), $request);

        // then
        $response->assertRedirect()
            ->assertSessionHasErrors('name');

        self::assertEquals(1, Template::where('name', $template->name)->count());
    }
}

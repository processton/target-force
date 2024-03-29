<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Template;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_template_is_in_use_if_it_has_at_least_one_post()
    {
        // given
        $template = Template::factory()->create();

        Post::factory()->create([
            'template_id' => $template->id
        ]);

        // then
        static::assertTrue($template->isInUse());
    }

    /** @test */
    public function the_template_is_not_in_use_if_it_has_not_posts()
    {
        // given
        $template = Template::factory()->create();

        // then
        static::assertFalse($template->isInUse());
    }
}

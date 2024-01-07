<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Targetforce\Base\Models\PostStatus;
use Tests\TestCase;

class PostStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_cancelled_status()
    {
        // given
        $postStatus = PostStatus::findOrFail(PostStatus::STATUS_CANCELLED);

        // then
        static::assertEquals('Cancelled', $postStatus->name);
    }
}

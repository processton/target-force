<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Targetforce\Base\Repositories\Messages\MySqlMessageTenantRepository;
use Targetforce\Base\Repositories\Messages\PostgresMessageTenantRepository;
use Tests\TestCase;

class MessageTenantRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var MySqlMessageTenantRepository|PostgresMessageTenantRepository */
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(MessageTenantRepositoryInterface::class);
    }

    /** @test */
    public function it_should_not_count_messages_that_have_not_been_opened_yet()
    {
        // given
        $post = Post::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        Message::factory()->count(2)->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'source_id' => $post->id,
        ]);

        // when
        $data = $this->repository->countUniqueOpensPerPeriod(Targetforce::currentWorkspaceId(), get_class($post), $post->id, (int)CarbonInterval::day()->totalSeconds);

        // then
        self::assertInstanceOf(Collection::class, $data);
        self::assertTrue($data->isEmpty());
    }

    /** @test */
    public function it_should_count_messages_that_have_been_opened_grouped_by_day_period()
    {
        // given
        $opened_at = CarbonImmutable::create(2020, 05, 9, 20);
        $post = Post::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        // 20 - 21 - 22 - 23
        foreach (range(0, 3) as $i) {
            Message::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
                'source_id' => $post->id,
                'opened_at' => $opened_at->addHours($i)
            ]);
        }

        // 24
        $next_opened_at = $opened_at->addHours(4);

        Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'source_id' => $post->id,
            'opened_at' => $next_opened_at
        ]);

        // when
        $data = $this->repository->countUniqueOpensPerPeriod(Targetforce::currentWorkspaceId(), get_class($post), $post->id, (int)CarbonInterval::day()->totalSeconds);

        // then
        self::assertEquals(2, $data->count());

        self::assertEquals(4, $data->first()->open_count);
        self::assertEquals($opened_at->toDateTimeString(), $data->first()->opened_at);
        self::assertEquals($opened_at->startOfDay()->toDateTimeString(), $data->first()->period_start);

        self::assertEquals(1, $data->last()->open_count);
        self::assertEquals($next_opened_at->toDateTimeString(), $data->last()->opened_at);
        self::assertEquals($next_opened_at->startOfDay()->toDateTimeString(), $data->last()->period_start);
    }

    /** @test */
    public function it_should_count_messages_that_have_been_opened_grouped_by_two_hours_period()
    {
        // given
        $opened_at = CarbonImmutable::create(2020, 05, 9, 20);

        $post = Post::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        // 20 - 21 - 22 - 23
        foreach (range(0, 3) as $i) {
            Message::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
                'source_id' => $post->id,
                'opened_at' => $opened_at->addHours($i)
            ]);
        }

        // 24
        $next_opened_at = $opened_at->addHours(4);

        Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'source_id' => $post->id,
            'opened_at' => $next_opened_at
        ]);

        // when
        $data = $this->repository->countUniqueOpensPerPeriod(Targetforce::currentWorkspaceId(), get_class($post), $post->id, (int)CarbonInterval::hours(2)->totalSeconds);

        // then
        self::assertEquals(3, $data->count());

        // 20
        self::assertEquals(2, $data[0]->open_count);
        self::assertEquals($opened_at->toDateTimeString(), $data[0]->opened_at);
        self::assertEquals($opened_at->toDateTimeString(), $data[0]->period_start);

        // 22
        self::assertEquals(2, $data[0]->open_count);
        self::assertEquals($opened_at->addHours(2)->toDateTimeString(), $data[1]->opened_at);
        self::assertEquals($opened_at->addHours(2)->toDateTimeString(), $data[1]->period_start);

        // 24
        self::assertEquals(1, $data[2]->open_count);
        self::assertEquals($next_opened_at->toDateTimeString(), $data[2]->opened_at);
        self::assertEquals($next_opened_at->startOfDay()->toDateTimeString(), $data[2]->period_start);
    }

    /** @test */
    public function it_should_count_messages_that_have_been_opened_grouped_by_hour_period()
    {
        // given
        $opened_at = CarbonImmutable::create(2020, 05, 9, 20);

        $post = Post::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        // 20 - 21 - 22 - 23
        foreach (range(0, 3) as $i) {
            Message::factory()->create([
                'workspace_id' => Targetforce::currentWorkspaceId(),
                'source_id' => $post->id,
                'opened_at' => $opened_at->addHours($i)
            ]);
        }

        // 24
        $next_opened_at = $opened_at->addHours(4);

        Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'source_id' => $post->id,
            'opened_at' => $next_opened_at
        ]);

        // when
        $data = $this->repository->countUniqueOpensPerPeriod(Targetforce::currentWorkspaceId(), get_class($post), $post->id, (int)CarbonInterval::hour()->totalSeconds);

        // then
        self::assertEquals(5, $data->count());

        foreach (range(0, 3) as $i) {
            self::assertEquals(1, $data[$i]->open_count);
            self::assertEquals($opened_at->addHours($i)->toDateTimeString(), $data[$i]->period_start);
        }

        self::assertEquals(1, $data->last()->open_count);
        self::assertEquals($next_opened_at->toDateTimeString(), $data->last()->opened_at);
        self::assertEquals($next_opened_at->startOfDay()->toDateTimeString(), $data->last()->period_start);
    }
}

<?php

declare(strict_types=1);

namespace Targetforce\Base\Repositories\Posts;

use Targetforce\Base\Models\Post;

class PostgresPostTenantRepository extends BasePostTenantRepository
{
    /**
     * @inheritDoc
     */
    public function getAverageTimeToOpen(Post $post): string
    {
        $average = $post->opens()
            ->selectRaw('ROUND(AVG(EXTRACT(EPOCH FROM (opened_at - delivered_at)))) as average_time_to_open')
            ->value('average_time_to_open');

        return $average ? $this->secondsToHms($average) : 'N/A';
    }

    /**
     * @inheritDoc
     */
    public function getAverageTimeToClick(Post $post): string
    {
        $average = $post->clicks()
            ->selectRaw('ROUND(AVG(EXTRACT(EPOCH FROM (clicked_at - delivered_at)))) as average_time_to_click')
            ->value('average_time_to_click');

        return $average ? $this->secondsToHms($average) : 'N/A';
    }
}

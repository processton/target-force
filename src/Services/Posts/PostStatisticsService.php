<?php

namespace Targetforce\Base\Services\Posts;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;

class PostStatisticsService
{
    /**
     * @var PostTenantRepositoryInterface
     */
    protected $posts;

    public function __construct(PostTenantRepositoryInterface $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @throws Exception
     */
    public function getForPost(Post $post, int $workspaceId): Collection
    {
        return $this->get(collect([$post]), $workspaceId);
    }

    /**
     * @throws Exception
     */
    public function getForCollection(Collection $posts, int $workspaceId): Collection
    {
        return $this->get($posts, $workspaceId);
    }

    /**
     * @throws Exception
     */
    public function getForPaginator(LengthAwarePaginator $paginator, int $workspaceId): Collection
    {
        return $this->get(collect($paginator->items()), $workspaceId);
    }

    /**
     * @throws Exception
     */
    protected function get(Collection $posts, int $workspaceId): Collection
    {
        $countData = $this->posts->getCounts($posts->pluck('id'), $workspaceId);

        return $posts->map(function (Post $post) use ($countData) {
            return [
                'post_id' => $post->id,
                'counts' => [
                    'total' => $countData[$post->id]->total,
                    'open' => $countData[$post->id]->opened,
                    'click' => $countData[$post->id]->clicked,
                    'sent' => $countData[$post->id]->sent,
                ],
                'ratios' => [
                    'open' => $post->getActionRatio($countData[$post->id]->opened, $countData[$post->id]->sent),
                    'click' => $post->getActionRatio($countData[$post->id]->clicked, $countData[$post->id]->sent),
                ],
            ];
        })->keyBy('post_id');
    }
}

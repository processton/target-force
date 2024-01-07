<?php

declare(strict_types=1);

namespace Targetforce\Base\Repositories\Posts;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Targetforce\Base\Interfaces\BaseTenantInterface;
use Targetforce\Base\Models\Post;

interface PostTenantRepositoryInterface extends BaseTenantInterface
{
    /**
     * Get the average time it takes for a message to be opened once it has been delivered for the post.
     */
    public function getAverageTimeToOpen(Post $post): string;

    /**
     * Get the average time it takes for a link to be clicked for the post.
     */
    public function getAverageTimeToClick(Post $post): string;

    /**
     * Posts that have been completed (have a SENT status).
     */
    public function completedPosts(int $workspaceId, array $relations = []): EloquentCollection;

    /**
     * Get open counts and ratios for a post.
     */
    public function getCounts(Collection $postIds, int $workspaceId): array;

    /**
     * Cancel a post.
     */
    public function cancelPost(Post $post): bool;
}

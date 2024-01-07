<?php

declare(strict_types=1);

namespace Targetforce\Base\Repositories\Posts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Repositories\BaseTenantRepository;
use Targetforce\Base\Traits\SecondsToHms;

abstract class BasePostTenantRepository extends BaseTenantRepository implements PostTenantRepositoryInterface
{
    use SecondsToHms;

    /** @var string */
    protected $modelName = Post::class;

    /**
     * {@inheritDoc}
     */
    public function completedPosts(int $workspaceId, array $relations = []): EloquentCollection
    {
        return $this->getQueryBuilder($workspaceId)
            ->where('status_id', PostStatus::STATUS_SENT)
            ->with($relations)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getCounts(Collection $postIds, int $workspaceId): array
    {
        $counts = DB::table('targetforce_posts')
            ->leftJoin('targetforce_messages', function ($join) use ($postIds, $workspaceId) {
                $join->on('targetforce_messages.source_id', '=', 'targetforce_posts.id')
                    ->where('targetforce_messages.source_type', Post::class)
                    ->whereIn('targetforce_messages.source_id', $postIds)
                    ->where('targetforce_messages.workspace_id', $workspaceId);
            })
            ->select('targetforce_posts.id as post_id')
            ->selectRaw(sprintf('count(%stargetforce_messages.id) as total', DB::getTablePrefix()))
            ->selectRaw(sprintf('count(case when %stargetforce_messages.opened_at IS NOT NULL then 1 end) as opened', DB::getTablePrefix()))
            ->selectRaw(sprintf('count(case when %stargetforce_messages.clicked_at IS NOT NULL then 1 end) as clicked', DB::getTablePrefix()))
            ->selectRaw(sprintf('count(case when %stargetforce_messages.sent_at IS NOT NULL then 1 end) as sent', DB::getTablePrefix()))
            ->selectRaw(sprintf('count(case when %stargetforce_messages.bounced_at IS NOT NULL then 1 end) as bounced', DB::getTablePrefix()))
            ->selectRaw(sprintf('count(case when %stargetforce_messages.sent_at IS NULL then 1 end) as pending', DB::getTablePrefix()))
            ->groupBy('targetforce_posts.id')
            ->orderBy('targetforce_posts.id')
            ->get();

        return $counts->flatten()->keyBy('post_id')->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function cancelPost(Post $post): bool
    {
        $this->deleteDraftMessages($post);

        return $post->update([
            'status_id' => PostStatus::STATUS_CANCELLED,
        ]);
    }

    private function deleteDraftMessages(Post $post): void
    {
        if (! $post->save_as_draft) {
            return;
        }

        $post->messages()->whereNull('sent_at')->delete();
    }

    /**
     * {@inheritDoc}
     */
    protected function applyFilters(Builder $instance, array $filters = []): void
    {
        $this->applySentFilter($instance, $filters);
    }

    /**
     * Filter by sent status.
     */
    protected function applySentFilter(Builder $instance, array $filters = []): void
    {
        if (Arr::get($filters, 'draft')) {
            $draftStatuses = [
                PostStatus::STATUS_DRAFT,
                PostStatus::STATUS_QUEUED,
                PostStatus::STATUS_SENDING,
            ];

            $instance->whereIn('status_id', $draftStatuses);
        } elseif (Arr::get($filters, 'sent')) {
            $sentStatuses = [
                PostStatus::STATUS_SENT,
                PostStatus::STATUS_CANCELLED,
            ];

            $instance->whereIn('status_id', $sentStatuses);
        }
    }
}

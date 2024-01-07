<?php

declare(strict_types=1);

namespace Targetforce\Base\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;
use Targetforce\Base\Services\Posts\PostDispatchService;

class PostDispatchCommand extends Command
{
    /** @var string */
    protected $signature = 'tf:posts:dispatch';

    /** @var string */
    protected $description = 'Dispatch all posts waiting in the queue';

    /** @var PostTenantRepositoryInterface */
    protected $postRepo;

    /** @var PostDispatchService */
    protected $postService;

    public function handle(
        PostTenantRepositoryInterface $postRepo,
        PostDispatchService $postService
    ): void {
        $this->postRepo = $postRepo;
        $this->postService = $postService;

        $posts = $this->getQueuedPosts();
        $count = count($posts);

        if (! $count) {
            return;
        }

        $this->info('Dispatching posts count=' . $count);

        foreach ($posts as $post) {
            $message = 'Dispatching post id=' . $post->id;

            $this->info($message);
            Log::info($message);
            $count++;

            $this->postService->handle($post);
        }

        $message = 'Finished dispatching posts';
        $this->info($message);
        Log::info($message);
    }

    /**
     * Get all queued posts.
     */
    protected function getQueuedPosts(): EloquentCollection
    {
        return Post::where('status_id', PostStatus::STATUS_QUEUED)
            ->where('scheduled_at', '<=', now())
            ->get();
    }
}

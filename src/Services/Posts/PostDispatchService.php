<?php

namespace Targetforce\Base\Services\Posts;

use Illuminate\Pipeline\Pipeline;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Pipelines\Posts\CompletePost;
use Targetforce\Base\Pipelines\Posts\CreateMessages;
use Targetforce\Base\Pipelines\Posts\StartPost;

class PostDispatchService
{
    /**
     * Dispatch the post
     *
     * @param Post $post
     * @return void
     */
    public function handle(Post $post)
    {
        // check if the post still exists
        if (! $post = $this->findPost($post->id)) {
            return;
        }

        if (! $post->queued) {
            \Log::error('Post does not have a queued status post_id=' . $post->id . ' status_id=' . $post->status_id);

            return;
        }

        $pipes = [
            StartPost::class,
            CreateMessages::class,
            CompletePost::class,
        ];

        try {
            app(Pipeline::class)
                ->send($post)
                ->through($pipes)
                ->then(function ($post) {
                    return $post;
                });
        } catch (\Exception $exception) {
            \Log::error('Error dispatching post id=' . $post->id . ' exception=' . $exception->getMessage() . ' trace=' . $exception->getTraceAsString());
        }
    }

    /**
     * Find a single post schedule
     *
     * @param int $id
     * @return Post|null
     */
    protected function findPost(int $id): ?Post
    {
        return Post::with('tags')->find($id);
    }
}

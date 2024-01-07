<?php

namespace Targetforce\Base\Pipelines\Posts;

use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;

class CompletePost
{
    /**
     * Mark the post as complete in the database
     *
     * @param Post $schedule
     * @return Post
     */
    public function handle(Post $schedule, $next)
    {
        $this->markPostAsComplete($schedule);

        return $next($schedule);
    }

    /**
     * Execute the database query
     *
     * @param Post $post
     * @return void
     */
    protected function markPostAsComplete(Post $post): void
    {
        $post->status_id = PostStatus::STATUS_SENT;
        $post->save();
    }
}

<?php

namespace Targetforce\Base\Pipelines\Posts;

use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;

class StartPost
{
    /**
     * Mark the post as started in the database
     *
     * @param Post $post
     * @return Post
     */
    public function handle(Post $post, $next)
    {
        $this->markPostAsSending($post);

        return $next($post);
    }

    /**
     * Execute the database request
     *
     * @param Post $post
     * @return Post
     */
    protected function markPostAsSending(Post $post): ?Post
    {
        return tap($post)->update([
            'status_id' => PostStatus::STATUS_SENDING,
        ]);
    }
}

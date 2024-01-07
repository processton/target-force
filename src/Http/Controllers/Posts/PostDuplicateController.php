<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Posts;

use Exception;
use Illuminate\Http\RedirectResponse;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;

class PostDuplicateController extends Controller
{
    /** @var PostTenantRepositoryInterface */
    protected $posts;

    public function __construct(PostTenantRepositoryInterface $posts)
    {
        $this->posts = $posts;
    }

    /**
     * Duplicate a post.
     *
     * @throws Exception
     */
    public function duplicate(int $postId): RedirectResponse
    {
        $post = $this->posts->find(Targetforce::currentWorkspaceId(), $postId);

        return redirect()->route('targetforce.posts.create')->withInput([
            'name' => $post->name . ' - Duplicate',
            'status_id' => PostStatus::STATUS_DRAFT,
            'template_id' => $post->template_id,
            'email_service_id' => $post->email_service_id,
            'subject' => $post->subject,
            'content' => $post->content,
            'from_name' => $post->from_name,
            'from_email' => $post->from_email,
        ]);
    }
}

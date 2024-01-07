<?php

namespace Targetforce\Base\Http\Controllers\Api;

use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\Api\PostDispatchRequest;
use Targetforce\Base\Http\Resources\Post as PostResource;
use Targetforce\Base\Interfaces\QuotaServiceInterface;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;

class PostDispatchController extends Controller
{
    /**
     * @var PostTenantRepositoryInterface
     */
    protected $posts;

    /**
     * @var QuotaServiceInterface
     */
    protected $quotaService;

    public function __construct(
        PostTenantRepositoryInterface $posts,
        QuotaServiceInterface $quotaService
    ) {
        $this->posts = $posts;
        $this->quotaService = $quotaService;
    }

    /**
     * @throws \Exception
     */
    public function send(PostDispatchRequest $request, $postId)
    {
        $post = $request->getPost(['email_service', 'messages']);
        $workspaceId = Targetforce::currentWorkspaceId();

        if ($this->quotaService->exceedsQuota($post->email_service, $post->unsent_count)) {
            return response([
                'message' => __('The number of subscribers for this post exceeds your SES quota')
            ], 422);
        }

        $post = $this->posts->update($workspaceId, $postId, [
            'status_id' => PostStatus::STATUS_QUEUED,
        ]);

        return new PostResource($post);
    }
}

<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Posts;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\PostDispatchRequest;
use Targetforce\Base\Interfaces\QuotaServiceInterface;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;

class PostDispatchController extends Controller
{
    /** @var PostTenantRepositoryInterface */
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
     * Dispatch the post.
     *
     * @throws Exception
     */
    public function send(PostDispatchRequest $request, int $id): RedirectResponse
    {
        $post = $this->posts->find(Targetforce::currentWorkspaceId(), $id, ['email_service', 'messages']);

        if ($post->status_id !== PostStatus::STATUS_DRAFT) {
            return redirect()->route('targetforce.posts.status', $id);
        }

        if (!$post->email_service_id) {
            return redirect()->route('targetforce.posts.edit', $id)
                ->withErrors(__('Please select an Email Service'));
        }

        $post->update([
            'send_to_all' => $request->get('recipients') === 'send_to_all',
        ]);

        $post->tags()->sync($request->get('tags'));

        if ($this->quotaService->exceedsQuota($post->email_service, $post->unsent_count)) {
            return redirect()->route('targetforce.posts.edit', $id)
                ->withErrors(__('The number of subscribers for this post exceeds your SES quota'));
        }

        $scheduledAt = $request->get('schedule') === 'scheduled' ? Carbon::parse($request->get('scheduled_at')) : now();

        $post->update([
            'scheduled_at' => $scheduledAt,
            'status_id' => PostStatus::STATUS_QUEUED,
            'save_as_draft' => $request->get('behaviour') === 'draft',
        ]);

        return redirect()->route('targetforce.posts.status', $id);
    }
}

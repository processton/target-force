<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Posts;

use Exception;
use Illuminate\Validation\ValidationException;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;

class PostCancellationController extends Controller
{
    /** @var PostTenantRepositoryInterface $postRepository */
    private $postRepository;

    public function __construct(PostTenantRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @throws Exception
     */
    public function confirm(int $postId)
    {
        $post = $this->postRepository->find(Targetforce::currentWorkspaceId(), $postId, ['status']);

        return view('targetforce::posts.cancel', [
            'post' => $post,
        ]);
    }

    /**
     * @throws Exception
     */
    public function cancel(int $postId)
    {
        /** @var Post $post */
        $post = $this->postRepository->find(Targetforce::currentWorkspaceId(), $postId, ['status']);
        $originalStatus = $post->status;

        if (!$post->canBeCancelled()) {
            throw ValidationException::withMessages([
                'postStatus' => "{$post->status->name} posts cannot be cancelled.",
            ])->redirectTo(route('targetforce.posts.index'));
        }

        if ($post->save_as_draft && !$post->allDraftsCreated()) {
            throw ValidationException::withMessages([
                'messagesPendingDraft' => __('Posts that save draft messages cannot be cancelled until all drafts have been created.'),
            ])->redirectTo(route('targetforce.posts.index'));
        }

        $this->postRepository->cancelPost($post);

        return redirect()->route('targetforce.posts.index')->with([
            'success' => $this->getSuccessMessage($originalStatus, $post),
        ]);
    }

    private function getSuccessMessage(PostStatus $postStatus, Post $post): string
    {
        if ($postStatus->id === PostStatus::STATUS_QUEUED) {
            return __('The queued post was cancelled successfully.');
        }

        if ($post->save_as_draft) {
            return __('The post was cancelled and any remaining draft messages were deleted.');
        }

        $messageCounts = $this->postRepository->getCounts(collect($post->id), $post->workspace_id)[$post->id];

        return __(
            "The post was cancelled whilst being processed (~:sent/:total dispatched).",
            [
                'sent' => $messageCounts->sent,
                'total' => $post->active_subscriber_count
            ]
        );
    }
}

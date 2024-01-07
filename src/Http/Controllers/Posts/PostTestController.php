<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Posts;

use Exception;
use Illuminate\Http\RedirectResponse;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\PostTestRequest;
use Targetforce\Base\Services\Messages\DispatchTestMessage;

class PostTestController extends Controller
{
    /** @var DispatchTestMessage */
    protected $dispatchTestMessage;

    public function __construct(DispatchTestMessage $dispatchTestMessage)
    {
        $this->dispatchTestMessage = $dispatchTestMessage;
    }

    /**
     * @throws Exception
     */
    public function handle(PostTestRequest $request, int $postId): RedirectResponse
    {
        $messageId = $this->dispatchTestMessage->handle(Targetforce::currentWorkspaceId(), $postId, $request->get('recipient_email'));

        if (!$messageId) {
            return redirect()->route('targetforce.posts.preview', $postId)
                ->withInput()
                ->with(['error', __('Failed to dispatch test email.')]);
        }

        return redirect()->route('targetforce.posts.preview', $postId)
            ->withInput()
            ->with(['success' => __('The test email has been dispatched.')]);
    }
}

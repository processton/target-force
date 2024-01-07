<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Posts;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;

class PostDeleteController extends Controller
{
    /** @var PostTenantRepositoryInterface */
    protected $posts;

    public function __construct(PostTenantRepositoryInterface $posts)
    {
        $this->posts = $posts;
    }

    /**
     * Show a confirmation view prior to deletion.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function confirm(int $id)
    {
        $post = $this->posts->find(Targetforce::currentWorkspaceId(), $id);

        if (!$post->draft) {
            return redirect()->route('targetforce.posts.index')
                ->withErrors(__('Unable to delete a post that is not in draft status'));
        }

        return view('targetforce::posts.delete', compact('post'));
    }

    /**
     * Delete a post from the database.
     *
     * @throws Exception
     */
    public function destroy(Request $request): RedirectResponse
    {
        $post = $this->posts->find(Targetforce::currentWorkspaceId(), $request->get('id'));

        if (!$post->draft) {
            return redirect()->route('targetforce.posts.index')
                ->withErrors(__('Unable to delete a post that is not in draft status'));
        }

        $this->posts->destroy(Targetforce::currentWorkspaceId(), $request->get('id'));

        return redirect()->route('targetforce.posts.index')
            ->with('success', __('The Post has been successfully deleted'));
    }
}

<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Posts;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Presenters\PostReportPresenter;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;
use Targetforce\Base\Repositories\Messages\MessageTenantRepositoryInterface;

class PostReportsController extends Controller
{
    /** @var PostTenantRepositoryInterface */
    protected $postRepo;

    /** @var MessageTenantRepositoryInterface */
    protected $messageRepo;

    public function __construct(
        PostTenantRepositoryInterface $postRepository,
        MessageTenantRepositoryInterface $messageRepo
    ) {
        $this->postRepo = $postRepository;
        $this->messageRepo = $messageRepo;
    }

    /**
     * Show post report view.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function index(int $id, Request $request)
    {
        $post = $this->postRepo->find(Targetforce::currentWorkspaceId(), $id);

        if ($post->draft) {
            return redirect()->route('targetforce.posts.edit', $id);
        }

        if ($post->queued || $post->sending) {
            return redirect()->route('targetforce.posts.status', $id);
        }

        $presenter = new PostReportPresenter($post, Targetforce::currentWorkspaceId(), (int) $request->get('interval', 24));
        $presenterData = $presenter->generate();

        $data = [
            'post' => $post,
            'postUrls' => $presenterData['postUrls'],
            'postStats' => $presenterData['postStats'],
            'chartLabels' => json_encode(Arr::get($presenterData['chartData'], 'labels', [])),
            'chartData' => json_encode(Arr::get($presenterData['chartData'], 'data', [])),
        ];

        return view('targetforce::posts.reports.index', $data);
    }

    /**
     * Show post recipients.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function recipients(int $id)
    {
        $post = $this->postRepo->find(Targetforce::currentWorkspaceId(), $id);

        if ($post->draft) {
            return redirect()->route('targetforce.posts.edit', $id);
        }

        if ($post->queued || $post->sending) {
            return redirect()->route('targetforce.posts.status', $id);
        }

        $messages = $this->messageRepo->recipients(Targetforce::currentWorkspaceId(), Post::class, $id);

        return view('targetforce::posts.reports.recipients', compact('post', 'messages'));
    }

    /**
     * Show post opens.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function opens(int $id)
    {
        $post = $this->postRepo->find(Targetforce::currentWorkspaceId(), $id);
        $averageTimeToOpen = $this->postRepo->getAverageTimeToOpen($post);

        if ($post->draft) {
            return redirect()->route('targetforce.posts.edit', $id);
        }

        if ($post->queued || $post->sending) {
            return redirect()->route('targetforce.posts.status', $id);
        }

        $messages = $this->messageRepo->opens(Targetforce::currentWorkspaceId(), Post::class, $id);

        return view('targetforce::posts.reports.opens', compact('post', 'messages', 'averageTimeToOpen'));
    }

    /**
     * Show post clicks.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function clicks(int $id)
    {
        $post = $this->postRepo->find(Targetforce::currentWorkspaceId(), $id);
        $averageTimeToClick = $this->postRepo->getAverageTimeToClick($post);

        if ($post->draft) {
            return redirect()->route('targetforce.posts.edit', $id);
        }

        if ($post->queued || $post->sending) {
            return redirect()->route('targetforce.posts.status', $id);
        }

        $messages = $this->messageRepo->clicks(Targetforce::currentWorkspaceId(), Post::class, $id);

        return view('targetforce::posts.reports.clicks', compact('post', 'messages', 'averageTimeToClick'));
    }

    /**
     * Show post bounces.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function bounces(int $id)
    {
        $post = $this->postRepo->find(Targetforce::currentWorkspaceId(), $id);

        if ($post->draft) {
            return redirect()->route('targetforce.posts.edit', $id);
        }

        if ($post->queued || $post->sending) {
            return redirect()->route('targetforce.posts.status', $id);
        }

        $messages = $this->messageRepo->bounces(Targetforce::currentWorkspaceId(), Post::class, $id);

        return view('targetforce::posts.reports.bounces', compact('post', 'messages'));
    }

    /**
     * Show post unsubscribes.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function unsubscribes(int $id)
    {
        $post = $this->postRepo->find(Targetforce::currentWorkspaceId(), $id);

        if ($post->draft) {
            return redirect()->route('targetforce.posts.edit', $id);
        }

        if ($post->queued || $post->sending) {
            return redirect()->route('targetforce.posts.status', $id);
        }

        $messages = $this->messageRepo->unsubscribes(Targetforce::currentWorkspaceId(), Post::class, $id);

        return view('targetforce::posts.reports.unsubscribes', compact('post', 'messages'));
    }
}

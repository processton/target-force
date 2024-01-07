<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Posts;

use Exception;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\PostStoreRequest;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;
use Targetforce\Base\Repositories\EmailServiceTenantRepository;
use Targetforce\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Targetforce\Base\Repositories\TagTenantRepository;
use Targetforce\Base\Repositories\TemplateTenantRepository;
use Targetforce\Base\Services\Posts\PostStatisticsService;

class PostsController extends Controller
{
    /** @var PostTenantRepositoryInterface */
    protected $posts;

    /** @var TemplateTenantRepository */
    protected $templates;

    /** @var TagTenantRepository */
    protected $tags;

    /** @var EmailServiceTenantRepository */
    protected $emailServices;

    /** @var SubscriberTenantRepositoryInterface */
    protected $subscribers;

    /**
     * @var PostStatisticsService
     */
    protected $postStatisticsService;

    public function __construct(
        PostTenantRepositoryInterface $posts,
        TemplateTenantRepository $templates,
        TagTenantRepository $tags,
        EmailServiceTenantRepository $emailServices,
        SubscriberTenantRepositoryInterface $subscribers,
        PostStatisticsService $postStatisticsService
    ) {
        $this->posts = $posts;
        $this->templates = $templates;
        $this->tags = $tags;
        $this->emailServices = $emailServices;
        $this->subscribers = $subscribers;
        $this->postStatisticsService = $postStatisticsService;
    }

    /**
     * @throws Exception
     */
    public function index(): ViewContract
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $params = ['draft' => true];
        $posts = $this->posts->paginate($workspaceId, 'created_atDesc', ['status'], 25, $params);

        return view('targetforce::posts.index', [
            'posts' => $posts,
            'postStats' => $this->postStatisticsService->getForPaginator($posts, $workspaceId),
        ]);
    }

    /**
     * @throws Exception
     */
    public function sent(): ViewContract
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $params = ['sent' => true];
        $posts = $this->posts->paginate($workspaceId, 'created_atDesc', ['status'], 25, $params);

        return view('targetforce::posts.index', [
            'posts' => $posts,
            'postStats' => $this->postStatisticsService->getForPaginator($posts, $workspaceId),
        ]);
    }

    /**
     * @throws Exception
     */
    public function create(): ViewContract
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $templates = [null => '- None -'] + $this->templates->pluck($workspaceId);
        $emailServices = $this->emailServices->all(Targetforce::currentWorkspaceId(), 'id', ['type'])
            ->map(static function (EmailService $emailService) {
                $emailService->formatted_name = "{$emailService->name} ({$emailService->type->name})";
                return $emailService;
            });

        return view('targetforce::posts.create', compact('templates', 'emailServices'));
    }

    /**
     * @throws Exception
     */
    public function store(PostStoreRequest $request): RedirectResponse
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $post = $this->posts->store($workspaceId, $this->handleCheckboxes($request->validated()));

        return redirect()->route('targetforce.posts.preview', $post->id);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): ViewContract
    {
        $post = $this->posts->find(Targetforce::currentWorkspaceId(), $id);

        return view('targetforce::posts.show', compact('post'));
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): ViewContract
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $post = $this->posts->find($workspaceId, $id);
        $emailServices = $this->emailServices->all($workspaceId, 'id', ['type'])
            ->map(static function (EmailService $emailService) {
                $emailService->formatted_name = "{$emailService->name} ({$emailService->type->name})";
                return $emailService;
            });
        $templates = [null => '- None -'] + $this->templates->pluck($workspaceId);

        return view('targetforce::posts.edit', compact('post', 'emailServices', 'templates'));
    }

    /**
     * @throws Exception
     */
    public function update(int $postId, PostStoreRequest $request): RedirectResponse
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $post = $this->posts->update(
            $workspaceId,
            $postId,
            $this->handleCheckboxes($request->validated())
        );

        return redirect()->route('targetforce.posts.preview', $post->id);
    }

    /**
     * @return RedirectResponse|ViewContract
     * @throws Exception
     */
    public function preview(int $id)
    {
        $post = $this->posts->find(Targetforce::currentWorkspaceId(), $id);
        $subscriberCount = $this->subscribers->countActive(Targetforce::currentWorkspaceId());

        if (!$post->draft) {
            return redirect()->route('targetforce.posts.status', $id);
        }

        $tags = $this->tags->all(Targetforce::currentWorkspaceId(), 'name');

        return view('targetforce::posts.preview', compact('post', 'tags', 'subscriberCount'));
    }

    /**
     * @return RedirectResponse|ViewContract
     * @throws Exception
     */
    public function status(int $id)
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $post = $this->posts->find($workspaceId, $id, ['status']);

        if ($post->sent) {
            return redirect()->route('targetforce.posts.reports.index', $id);
        }

        return view('targetforce::posts.status', [
            'post' => $post,
            'postStats' => $this->postStatisticsService->getForPost($post, $workspaceId),
        ]);
    }

    /**
     * Handle checkbox fields.
     *
     * NOTE(david): this is here because the Post model is marked as being unable to use boolean fields.
     */
    private function handleCheckboxes(array $input): array
    {
        $checkboxFields = [
            'is_open_tracking',
            'is_click_tracking'
        ];

        foreach ($checkboxFields as $checkboxField) {
            if (!isset($input[$checkboxField])) {
                $input[$checkboxField] = false;
            }
        }

        return $input;
    }
}

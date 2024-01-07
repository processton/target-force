<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\Api\PostStoreRequest;
use Targetforce\Base\Http\Resources\Post as PostResource;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;

class PostsController extends Controller
{
    /** @var PostTenantRepositoryInterface */
    private $posts;

    public function __construct(PostTenantRepositoryInterface $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = Targetforce::currentWorkspaceId();

        return PostResource::collection($this->posts->paginate($workspaceId, 'id', ['tags']));
    }

    /**
     * @throws Exception
     */
    public function store(PostStoreRequest $request): PostResource
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $data = Arr::except($request->validated(), ['tags']);

        $data['save_as_draft'] = $request->get('save_as_draft') ?? 0;

        $post = $this->posts->store($workspaceId, $data);

        $post->tags()->sync($request->get('tags'));

        return new PostResource($post);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): PostResource
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $post = $this->posts->find($workspaceId, $id);

        return new PostResource($post);
    }

    /**
     * @throws Exception
     */
    public function update(PostStoreRequest $request, int $id): PostResource
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $data = Arr::except($request->validated(), ['tags']);

        $data['save_as_draft'] = $request->get('save_as_draft') ?? 0;

        $post = $this->posts->update($workspaceId, $id, $data);

        $post->tags()->sync($request->get('tags'));

        return new PostResource($post);
    }
}

<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\Api\TagStoreRequest;
use Targetforce\Base\Http\Requests\Api\TagUpdateRequest;
use Targetforce\Base\Http\Resources\Tag as TagResource;
use Targetforce\Base\Repositories\TagTenantRepository;
use Targetforce\Base\Services\Tags\ApiTagService;

class TagsController extends Controller
{
    /** @var TagTenantRepository */
    private $tags;

    /** @var ApiTagService */
    private $apiService;

    public function __construct(
        TagTenantRepository $tags,
        ApiTagService $apiService
    ) {
        $this->tags = $tags;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = Targetforce::currentWorkspaceId();

        return TagResource::collection(
            $this->tags->paginate($workspaceId, 'name', [], request()->get('per_page', 25))
        );
    }

    /**
     * @throws Exception
     */
    public function store(TagStoreRequest $request): TagResource
    {
        $input = $request->validated();
        $workspaceId = Targetforce::currentWorkspaceId();
        $tag = $this->apiService->store($workspaceId, collect($input));

        $tag->load('subscribers');

        return new TagResource($tag);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): TagResource
    {
        $workspaceId = Targetforce::currentWorkspaceId();

        return new TagResource($this->tags->find($workspaceId, $id));
    }

    /**
     * @throws Exception
     */
    public function update(TagUpdateRequest $request, int $id): TagResource
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $tag = $this->tags->update($workspaceId, $id, $request->validated());

        return new TagResource($tag);
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): Response
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $this->tags->destroy($workspaceId, $id);

        return response(null, 204);
    }
}

<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\Api\TagSubscriberDestroyRequest;
use Targetforce\Base\Http\Requests\Api\TagSubscriberStoreRequest;
use Targetforce\Base\Http\Requests\Api\TagSubscriberUpdateRequest;
use Targetforce\Base\Http\Resources\Subscriber as SubscriberResource;
use Targetforce\Base\Repositories\TagTenantRepository;
use Targetforce\Base\Services\Tags\ApiTagSubscriberService;

class TagSubscribersController extends Controller
{
    /** @var TagTenantRepository */
    private $tags;

    /** @var ApiTagSubscriberService */
    private $apiService;

    public function __construct(
        TagTenantRepository $tags,
        ApiTagSubscriberService $apiService
    ) {
        $this->tags = $tags;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(int $tagId): AnonymousResourceCollection
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $tag = $this->tags->find($workspaceId, $tagId, ['subscribers']);

        return SubscriberResource::collection($tag->subscribers);
    }

    /**
     * @throws Exception
     */
    public function store(TagSubscriberStoreRequest $request, int $tagId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Targetforce::currentWorkspaceId();
        $subscribers = $this->apiService->store($workspaceId, $tagId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }

    /**
     * @throws Exception
     */
    public function update(TagSubscriberUpdateRequest $request, int $tagId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Targetforce::currentWorkspaceId();
        $subscribers = $this->apiService->update($workspaceId, $tagId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }

    /**
     * @throws Exception
     */
    public function destroy(TagSubscriberDestroyRequest $request, int $tagId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Targetforce::currentWorkspaceId();
        $subscribers = $this->apiService->destroy($workspaceId, $tagId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }
}

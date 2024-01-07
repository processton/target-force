<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\Api\SubscriberTagDestroyRequest;
use Targetforce\Base\Http\Requests\Api\SubscriberTagStoreRequest;
use Targetforce\Base\Http\Requests\Api\SubscriberTagUpdateRequest;
use Targetforce\Base\Http\Resources\Tag as TagResource;
use Targetforce\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Targetforce\Base\Services\Subscribers\Tags\ApiSubscriberTagService;

class SubscriberTagsController extends Controller
{
    /** @var SubscriberTenantRepositoryInterface */
    private $subscribers;

    /** @var ApiSubscriberTagService */
    private $apiService;

    public function __construct(
        SubscriberTenantRepositoryInterface $subscribers,
        ApiSubscriberTagService $apiService
    ) {
        $this->subscribers = $subscribers;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(int $subscriberId): AnonymousResourceCollection
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $subscriber = $this->subscribers->find($workspaceId, $subscriberId, ['tags']);

        return TagResource::collection($subscriber->tags);
    }

    /**
     * @throws Exception
     */
    public function store(SubscriberTagStoreRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Targetforce::currentWorkspaceId();
        $tags = $this->apiService->store($workspaceId, $subscriberId, collect($input['tags']));

        return TagResource::collection($tags);
    }

    /**
     * @throws Exception
     */
    public function update(SubscriberTagUpdateRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Targetforce::currentWorkspaceId();
        $tags = $this->apiService->update($workspaceId, $subscriberId, collect($input['tags']));

        return TagResource::collection($tags);
    }

    /**
     * @throws Exception
     */
    public function destroy(SubscriberTagDestroyRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = Targetforce::currentWorkspaceId();
        $tags = $this->apiService->destroy($workspaceId, $subscriberId, collect($input['tags']));

        return TagResource::collection($tags);
    }
}

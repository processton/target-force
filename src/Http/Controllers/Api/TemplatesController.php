<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\Api\TemplateStoreRequest;
use Targetforce\Base\Http\Requests\Api\TemplateUpdateRequest;
use Targetforce\Base\Http\Resources\Template as TemplateResource;
use Targetforce\Base\Repositories\TemplateTenantRepository;
use Targetforce\Base\Services\Templates\TemplateService;

class TemplatesController extends Controller
{
    /** @var TemplateTenantRepository */
    private $templates;

    /** @var TemplateService */
    private $service;

    public function __construct(TemplateTenantRepository $templates, TemplateService $service)
    {
        $this->templates = $templates;
        $this->service = $service;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $templates = $this->templates->paginate($workspaceId, 'name');

        return TemplateResource::collection($templates);
    }


    /**
     * @throws Exception
     */
    public function show(int $id): TemplateResource
    {
        $workspaceId = Targetforce::currentWorkspaceId();

        return new TemplateResource($this->templates->find($workspaceId, $id));
    }

    /**
     * @throws Exception
     */
    public function store(TemplateStoreRequest $request): TemplateResource
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $template = $this->service->store($workspaceId, $request->validated());

        return new TemplateResource($template);
    }

    /**
     * @throws Exception
     */
    public function update(TemplateUpdateRequest $request, int $id): TemplateResource
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $template = $this->service->update($workspaceId, $id, $request->validated());

        return new TemplateResource($template);
    }

    /**
     * @throws \Throwable
     */
    public function destroy(int $id): Response
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $this->service->delete($workspaceId, $id);

        return response(null, 204);
    }
}

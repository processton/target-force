<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Resources\Workspace as WorkspaceResource;
use Targetforce\Base\Repositories\WorkspacesRepository;

class WorkspacesController extends Controller
{
    /** @var WorkspacesRepository */
    private $workspaces;

    public function __construct(WorkspacesRepository $workspaces)
    {
        $this->workspaces = $workspaces;
    }

    /**
     * @throws Exception
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $workspaces = $this->workspaces->workspacesForUser($request->user());

        return WorkspaceResource::collection($workspaces);
    }
}

<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Tags;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\TagStoreRequest;
use Targetforce\Base\Http\Requests\TagUpdateRequest;
use Targetforce\Base\Repositories\TagTenantRepository;

class TagsController extends Controller
{
    /** @var TagTenantRepository */
    private $tagRepository;

    public function __construct(TagTenantRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $tags = $this->tagRepository->paginate(Targetforce::currentWorkspaceId(), 'name');

        return view('targetforce::tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('targetforce::tags.create');
    }

    /**
     * @throws Exception
     */
    public function store(TagStoreRequest $request): RedirectResponse
    {
        $this->tagRepository->store(Targetforce::currentWorkspaceId(), $request->all());

        return redirect()->route('targetforce.tags.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $tag = $this->tagRepository->find(Targetforce::currentWorkspaceId(), $id, ['subscribers']);

        return view('targetforce::tags.edit', compact('tag'));
    }

    /**
     * @throws Exception
     */
    public function update(int $id, TagUpdateRequest $request): RedirectResponse
    {
        $this->tagRepository->update(Targetforce::currentWorkspaceId(), $id, $request->all());

        return redirect()->route('targetforce.tags.index');
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->tagRepository->destroy(Targetforce::currentWorkspaceId(), $id);

        return redirect()->route('targetforce.tags.index');
    }
}

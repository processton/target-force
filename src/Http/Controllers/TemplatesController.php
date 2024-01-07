<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Requests\TemplateStoreRequest;
use Targetforce\Base\Http\Requests\TemplateUpdateRequest;
use Targetforce\Base\Repositories\TemplateTenantRepository;
use Targetforce\Base\Services\Templates\TemplateService;
use Targetforce\Base\Traits\NormalizeTags;
use Throwable;

class TemplatesController extends Controller
{
    use NormalizeTags;

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
    public function index(): View
    {
        $templates = $this->templates->paginate(Targetforce::currentWorkspaceId(), 'name');

        return view('targetforce::templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('targetforce::templates.create');
    }

    /**
     * @throws Exception
     */
    public function store(TemplateStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->service->store(Targetforce::currentWorkspaceId(), $data);

        return redirect()
            ->route('targetforce.templates.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $template = $this->templates->find(Targetforce::currentWorkspaceId(), $id);

        return view('targetforce::templates.edit', compact('template'));
    }

    /**
     * @throws Exception
     */
    public function update(TemplateUpdateRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();

        $this->service->update(Targetforce::currentWorkspaceId(), $id, $data);

        return redirect()
            ->route('targetforce.templates.index');
    }

    /**
     * @throws Throwable
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete(Targetforce::currentWorkspaceId(), $id);

        return redirect()
            ->route('targetforce.templates.index')
            ->with('success', __('Template successfully deleted.'));
    }
}

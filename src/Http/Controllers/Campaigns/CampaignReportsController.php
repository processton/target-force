<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Models\Campaign;
use Targetforce\Base\Presenters\CampaignReportPresenter;
use Targetforce\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Targetforce\Base\Repositories\Messages\MessageTenantRepositoryInterface;

class CampaignReportsController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected $campaignRepo;

    /** @var MessageTenantRepositoryInterface */
    protected $messageRepo;

    public function __construct(
        CampaignTenantRepositoryInterface $campaignRepository,
        MessageTenantRepositoryInterface $messageRepo
    ) {
        $this->campaignRepo = $campaignRepository;
        $this->messageRepo = $messageRepo;
    }

    /**
     * Show campaign report view.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function index(int $id, Request $request)
    {
        $campaign = $this->campaignRepo->find(Targetforce::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('targetforce.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('targetforce.campaigns.status', $id);
        }

        $presenter = new CampaignReportPresenter($campaign, Targetforce::currentWorkspaceId(), (int) $request->get('interval', 24));
        $presenterData = $presenter->generate();

        $data = [
            'campaign' => $campaign,
            'campaignUrls' => $presenterData['campaignUrls'],
            'campaignStats' => $presenterData['campaignStats'],
            'chartLabels' => json_encode(Arr::get($presenterData['chartData'], 'labels', [])),
            'chartData' => json_encode(Arr::get($presenterData['chartData'], 'data', [])),
        ];

        return view('targetforce::campaigns.reports.index', $data);
    }

    /**
     * Show campaign recipients.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function recipients(int $id)
    {
        $campaign = $this->campaignRepo->find(Targetforce::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('targetforce.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('targetforce.campaigns.status', $id);
        }

        $messages = $this->messageRepo->recipients(Targetforce::currentWorkspaceId(), Campaign::class, $id);

        return view('targetforce::campaigns.reports.recipients', compact('campaign', 'messages'));
    }

    /**
     * Show campaign opens.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function opens(int $id)
    {
        $campaign = $this->campaignRepo->find(Targetforce::currentWorkspaceId(), $id);
        $averageTimeToOpen = $this->campaignRepo->getAverageTimeToOpen($campaign);

        if ($campaign->draft) {
            return redirect()->route('targetforce.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('targetforce.campaigns.status', $id);
        }

        $messages = $this->messageRepo->opens(Targetforce::currentWorkspaceId(), Campaign::class, $id);

        return view('targetforce::campaigns.reports.opens', compact('campaign', 'messages', 'averageTimeToOpen'));
    }

    /**
     * Show campaign clicks.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function clicks(int $id)
    {
        $campaign = $this->campaignRepo->find(Targetforce::currentWorkspaceId(), $id);
        $averageTimeToClick = $this->campaignRepo->getAverageTimeToClick($campaign);

        if ($campaign->draft) {
            return redirect()->route('targetforce.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('targetforce.campaigns.status', $id);
        }

        $messages = $this->messageRepo->clicks(Targetforce::currentWorkspaceId(), Campaign::class, $id);

        return view('targetforce::campaigns.reports.clicks', compact('campaign', 'messages', 'averageTimeToClick'));
    }

    /**
     * Show campaign bounces.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function bounces(int $id)
    {
        $campaign = $this->campaignRepo->find(Targetforce::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('targetforce.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('targetforce.campaigns.status', $id);
        }

        $messages = $this->messageRepo->bounces(Targetforce::currentWorkspaceId(), Campaign::class, $id);

        return view('targetforce::campaigns.reports.bounces', compact('campaign', 'messages'));
    }

    /**
     * Show campaign unsubscribes.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function unsubscribes(int $id)
    {
        $campaign = $this->campaignRepo->find(Targetforce::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('targetforce.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('targetforce.campaigns.status', $id);
        }

        $messages = $this->messageRepo->unsubscribes(Targetforce::currentWorkspaceId(), Campaign::class, $id);

        return view('targetforce::campaigns.reports.unsubscribes', compact('campaign', 'messages'));
    }
}

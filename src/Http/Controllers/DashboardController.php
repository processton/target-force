<?php

namespace Targetforce\Base\Http\Controllers;

use Carbon\CarbonPeriod;
use Exception;
use Illuminate\View\View;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;
use Targetforce\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Targetforce\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Targetforce\Base\Services\Posts\PostStatisticsService;

class DashboardController extends Controller
{
    /**
     * @var SubscriberTenantRepositoryInterface
     */
    protected $subscribers;

    /**
     * @var PostTenantRepositoryInterface
     */
    protected $posts;

    /**
     * @var MessageTenantRepositoryInterface
     */
    protected $messages;

    /**
     * @var PostStatisticsService
     */
    protected $postStatisticsService;

    public function __construct(SubscriberTenantRepositoryInterface $subscribers, PostTenantRepositoryInterface $posts, MessageTenantRepositoryInterface $messages, PostStatisticsService $postStatisticsService)
    {
        $this->subscribers = $subscribers;
        $this->posts = $posts;
        $this->messages = $messages;
        $this->postStatisticsService = $postStatisticsService;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $workspaceId = Targetforce::currentWorkspaceId();
        $completedPosts = $this->posts->completedPosts($workspaceId, ['status']);
        $subscriberGrowthChart = $this->getSubscriberGrowthChart($workspaceId);

        return view('targetforce::dashboard.index', [
            'recentSubscribers' => $this->subscribers->getRecentSubscribers($workspaceId),
            'completedPosts' => $completedPosts,
            'postStats' => $this->postStatisticsService->getForCollection($completedPosts, $workspaceId),
            'subscriberGrowthChartLabels' => json_encode($subscriberGrowthChart['labels']),
            'subscriberGrowthChartData' => json_encode($subscriberGrowthChart['data']),
        ]);
    }

    protected function getSubscriberGrowthChart($workspaceId): array
    {
        $period = CarbonPeriod::create(now()->subDays(30)->startOfDay(), now()->endOfDay());

        $growthChartData = $this->subscribers->getGrowthChartData($period, $workspaceId);

        $growthChart = [
            'labels' => [],
            'data' => [],
        ];

        $currentTotal = $growthChartData['startingValue'];

        foreach ($period as $date) {
            $formattedDate = $date->format('d-m-Y');

            $periodValue = $growthChartData['runningTotal'][$formattedDate]->total ?? 0;
            $periodUnsubscribe = $growthChartData['unsubscribers'][$formattedDate]->total ?? 0;
            $currentTotal += $periodValue - $periodUnsubscribe;

            $growthChart['labels'][] = $formattedDate;
            $growthChart['data'][] = $currentTotal;
        }

        return $growthChart;
    }
}

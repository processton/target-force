<?php

declare(strict_types=1);

namespace Targetforce\Base\Repositories\Campaigns;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Targetforce\Base\Interfaces\BaseTenantInterface;
use Targetforce\Base\Models\Campaign;

interface CampaignTenantRepositoryInterface extends BaseTenantInterface
{
    /**
     * Get the average time it takes for a message to be opened once it has been delivered for the campaign.
     */
    public function getAverageTimeToOpen(Campaign $campaign): string;

    /**
     * Get the average time it takes for a link to be clicked for the campaign.
     */
    public function getAverageTimeToClick(Campaign $campaign): string;

    /**
     * Campaigns that have been completed (have a SENT status).
     */
    public function completedCampaigns(int $workspaceId, array $relations = []): EloquentCollection;

    /**
     * Get open counts and ratios for a campaign.
     */
    public function getCounts(Collection $campaignIds, int $workspaceId): array;

    /**
     * Cancel a campaign.
     */
    public function cancelCampaign(Campaign $campaign): bool;
}

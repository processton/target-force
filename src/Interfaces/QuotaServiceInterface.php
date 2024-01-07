<?php

namespace Targetforce\Base\Interfaces;

use Targetforce\Base\Models\EmailService;

interface QuotaServiceInterface
{
    public function exceedsQuota(EmailService $emailService, int $messageCount): bool;
}

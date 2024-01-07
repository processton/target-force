<?php

declare(strict_types=1);

namespace Targetforce\Base\Models;

class PostStatus extends BaseModel
{
    protected $table = 'targetforce_post_statuses';

    /** @var bool */
    public $timestamps = false;

    public const STATUS_DRAFT = 1;
    public const STATUS_QUEUED = 2;
    public const STATUS_SENDING = 3;
    public const STATUS_SENT = 4;
    public const STATUS_CANCELLED = 5;
}

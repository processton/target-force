<?php

declare(strict_types=1);

namespace Targetforce\Base\Models;

use Carbon\Carbon;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $workspace_id
 * @property string $name
 * @property int $status_id
 * @property int|null $template_id
 * @property int|null $email_service_id
 * @property string|null $subject
 * @property string|null $content
 * @property string|null $from_name
 * @property string|null $from_email
 * @property bool $is_open_tracking
 * @property bool $is_click_tracking
 * @property int|null $open_count
 * @property int|null $click_count
 * @property bool $save_to_draft
 * @property bool $send_to_all
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property EloquentCollection $tags
 * @property PostStatus $status
 * @property Template|null $template
 * @property EmailService|null $email_service
 * @property EloquentCollection $messages
 * @property EloquentCollection $sent_messages
 * @property EloquentCollection $opens
 * @property EloquentCollection $clicks
 *
 * @property-read int $active_subscriber_count_attribute
 * @property-read int $sent_count
 * @property-read int $unsent_count
 * @property-read string $sent_count_formatted
 * @property-read float|int $open_ratio
 * @property-read float|int $click_ratio
 * @property-read float|int $bounce_ratio
 * @property-read string|null $merged_content
 * @property-read bool $draft
 * @property-read bool $queued
 * @property-read bool $sending
 * @property-read bool $sent
 * @property-read bool $cancelled
 * @property-read bool $unique_open_count
 * @property-read bool $total_open_count
 * @property-read bool $unique_click_count
 * @property-read bool $total_click_count
 *
 * @method static PostFactory factory
 */
class Post extends BaseModel
{
    use HasFactory;

    // NOTE(david): we require this because of namespace issues when resolving factories from models
    // not in the default `App\Models` namespace.
    protected static function newFactory()
    {
        return PostFactory::new();
    }

    /** @var string */
    protected $table = 'targetforce_posts';

    /** @var array */
    protected $guarded = [];

    /**
     * We can't use boolean fields on this model because we have multiple points to update from the controller.
     *
     * @var array
     */
    protected $booleanFields = [];

    /** @var array */
    protected $casts = [
        'status_id' => 'int',
        'workspace_id' => 'int',
        'template_id' => 'int',
        'email_service_id' => 'int',
        'is_open_tracking' => 'bool',
        'is_click_tracking' => 'bool',
        'scheduled_at' => 'datetime',
        'save_as_draft' => 'bool',
        'send_to_all' => 'bool',
    ];

    /**
     * Tags this post was sent to.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'targetforce_post_tag')->withTimestamps();
    }
    /**
     * Status of the post.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(PostStatus::class);
    }

    /**
     * Template used in the post.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Email Service used for the post.
     */
    public function email_service(): BelongsTo
    {
        return $this->belongsTo(EmailService::class);
    }

    /**
     * All of a posts's messages.
     */
    public function messages(): MorphMany
    {
        return $this->morphMany(Message::class, 'source');
    }

    /**
     * All of a post's sent messages.
     */
    public function sent_messages(): MorphMany
    {
        return $this->morphMany(Message::class, 'source')->whereNotNull('sent_at');
    }

    /**
     * All of a post's opened messages.
     */
    public function opens(): MorphMany
    {
        return $this->morphMany(Message::class, 'source')->whereNotNull('opened_at');
    }

    /**
     * All of the post's clicked messages.
     */
    public function clicks(): MorphMany
    {
        return $this->morphMany(Message::class, 'source')->whereNotNull('clicked_at');
    }

    public function getActiveSubscriberCountAttribute(): int
    {
        return Subscriber::where('workspace_id', $this->workspace_id)
            ->whereNull('unsubscribed_at')
            ->when(!$this->send_to_all, function (Builder $query) {
                $query->whereHas('tags', function (Builder $subQuery) {
                    $subQuery->whereIn('targetforce_tags.id', $this->tags->pluck('id'));
                });
            })
            ->count();
    }

    public function getSentCountAttribute(): int
    {
        return $this->sent_messages->count();
    }

    public function getUnsentCountAttribute(): int
    {
        if ($this->messages->count()) {
            return ($this->messages->count() - $this->sent_count);
        }

        return $this->active_subscriber_count;
    }

    public function getSentCountFormattedAttribute(): string
    {
        $value = $this->sent_count;

        if ($value > 999999) {
            return round($value / 1000000) . 'm';
        }

        if ($value > 9999 && $value <= 999999) {
            return round($value / 1000) . 'k';
        }

        return (string)$value;
    }

    /**
     * Get the posts's open ratio as an attribute.
     *
     * @return float|int
     * @todo this needs to be refactored, because its running a query per row when list the posts
     */
    public function getOpenRatioAttribute()
    {
        if ($openCount = $this->opens->count()) {
            return $openCount / $this->sent_count;
        }

        return 0;
    }

    /**
     * Get the posts's click ratio as an attribute.
     *
     * @return float|int
     * @todo this needs to be refactored, because its running a query per row when list the posts
     */
    public function getClickRatioAttribute()
    {
        if ($clickCount = $this->clicks->count()) {
            return $clickCount / $this->sent_count;
        }

        return 0;
    }

    /**
     * Get the posts's click ratio as an attribute.
     *
     * @return float|int
     * @todo this needs to be refactored, because its running a query per row when list the posts
     */
    public function getBounceRatioAttribute()
    {
        if ($bounceCount = $this->messages->whereNotNull('bounced_at')->count()) {
            return $bounceCount / $this->sent_count;
        }

        return 0;
    }

    /**
     * Get the merged content for this email, including the template content.
     */
    public function getMergedContentAttribute(): ?string
    {
        if ($this->template_id) {
            return str_replace(['{{content}}', '{{ content }}'], $this->content, $this->template->content);
        }

        return $this->content;
    }

    /**
     * Whether the post is a draft.
     */
    public function getDraftAttribute(): bool
    {
        return $this->status_id === PostStatus::STATUS_DRAFT;
    }

    /**
     * Whether the post has been queued for sending.
     */
    public function getQueuedAttribute(): bool
    {
        return $this->status_id === PostStatus::STATUS_QUEUED;
    }

    /**
     * Whether the post has been sent.
     */
    public function getSendingAttribute(): bool
    {
        return $this->status_id === PostStatus::STATUS_SENDING;
    }

    /**
     * Whether the post has been sent.
     */
    public function getSentAttribute(): bool
    {
        return $this->status_id === PostStatus::STATUS_SENT;
    }

    /**
     * Whether the post has been cancelled.
     */
    public function getCancelledAttribute(): bool
    {
        return $this->status_id === PostStatus::STATUS_CANCELLED;
    }

    /**
     * Get the number of unique opens for the post.
     */
    public function getUniqueOpenCountAttribute(): int
    {
        return $this->opens()->count();
    }

    /**
     * Get the total number of opens for the post.
     */
    public function getTotalOpenCountAttribute(): int
    {
        return (int)$this->opens()->sum('open_count');
    }

    /**
     * Get the number of unique clicks for the post.
     */
    public function getUniqueClickCountAttribute(): int
    {
        return $this->clicks()->count();
    }

    /**
     * Get the total number of opens for the post.
     */
    public function getTotalClickCountAttribute(): int
    {
        return (int)$this->clicks()->sum('click_count');
    }

    public function formatCount(int $count): string
    {
        if ($count > 999999) {
            return round($count / 1000000) . 'm';
        }

        if ($count > 9999 && $count <= 999999) {
            return round($count / 1000) . 'k';
        }

        return (string)$count;
    }

    public function getActionRatio(int $actionCount, int $sentCount)
    {
        if ($actionCount) {
            return $actionCount / $sentCount;
        }

        return 0;
    }

    /**
     * Determine whether the post can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        // we can cancel posts that still have draft messages, because they haven't been entirely dispatched
        // a post that doesn't have any more draft messages (i.e. they have all been sent) cannot be cancelled, because the post is completed

        if (
            $this->status_id === PostStatus::STATUS_SENT
            && $this->save_as_draft
            && $this->sent_count !== $this->messages()->count()
        ) {
            return true;
        }

        return in_array($this->status_id, [PostStatus::STATUS_QUEUED, PostStatus::STATUS_SENDING], true);
    }

    /**
     * Determine whether all drafts have been created for a post.
     */
    public function allDraftsCreated(): bool
    {
        if (! $this->save_as_draft) {
            return true;
        }

        return $this->active_subscriber_count === $this->messages()->count();
    }
}

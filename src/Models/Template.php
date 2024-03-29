<?php

declare(strict_types=1);

namespace Targetforce\Base\Models;

use Carbon\Carbon;
use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $workspace_id
 * @property string $name
 * @property string|null $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property EloquentCollection $posts
 *
 * @method static TemplateFactory factory
 */
class Template extends BaseModel
{
    use HasFactory;

    // NOTE(david): we require this because of namespace issues when resolving factories from models
    // not in the default `App\Models` namespace.
    protected static function newFactory()
    {
        return TemplateFactory::new();
    }

    /** @var string */
    protected $table = 'targetforce_templates';

    /** @var array */
    protected $guarded = [];

    /**
     * Posts using this template
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isInUse(): bool
    {
        return $this->posts()->count() > 0;
    }
}

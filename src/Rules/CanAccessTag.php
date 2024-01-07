<?php

declare(strict_types=1);

namespace Targetforce\Base\Rules;

use Illuminate\Contracts\Validation\Rule;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Tag;

class CanAccessTag implements Rule
{
    public function passes($attribute, $value): bool
    {
        $tag = Tag::find($value);

        if (!$tag) {
            return false;
        }

        return $tag->workspace_id == Targetforce::currentWorkspaceId();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Tag ID :input does not exist.';
    }
}

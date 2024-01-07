<?php

declare(strict_types=1);

namespace Targetforce\Base\Rules;

use Illuminate\Contracts\Validation\Rule;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Subscriber;

class CanAccessSubscriber implements Rule
{
    public function passes($attribute, $value): bool
    {
        $subscriber = Subscriber::find($value);

        if (!$subscriber) {
            return false;
        }

        return $subscriber->workspace_id == Targetforce::currentWorkspaceId();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}

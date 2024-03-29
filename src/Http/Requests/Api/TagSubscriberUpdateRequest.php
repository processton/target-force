<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Targetforce\Base\Rules\CanAccessSubscriber;

class TagSubscriberUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'subscribers' => ['array', 'required'],
            'subscribers.*' => ['integer', new CanAccessSubscriber()]
        ];
    }
}

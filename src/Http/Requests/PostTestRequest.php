<?php

namespace Targetforce\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recipient_email' => [
                'required',
                'email',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_email.required' => __('A test email address is required.'),
        ];
    }
}

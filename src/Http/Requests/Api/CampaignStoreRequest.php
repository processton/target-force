<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Requests\Api;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Requests\PostStoreRequest as BasePostStoreRequest;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;
use Targetforce\Base\Repositories\TagTenantRepository;

class PostStoreRequest extends BasePostStoreRequest
{
    /**
     * @var PostTenantRepositoryInterface
     */
    protected $posts;

    public function __construct(PostTenantRepositoryInterface $posts)
    {
        parent::__construct();

        $this->posts = $posts;
        $this->workspaceId = Targetforce::currentWorkspaceId();

        Validator::extendImplicit('valid_status', function ($attribute, $value, $parameters, $validator) {
            return $this->post
                ? $this->getPost()->status_id === PostStatus::STATUS_DRAFT
                : true;
        });
    }

    /**
     * @throws \Exception
     */
    public function getPost(): Post
    {
        return $this->post = $this->posts->find($this->workspaceId, $this->post);
    }

    public function rules(): array
    {
        $tags = app(TagTenantRepository::class)->pluck(
            $this->workspaceId,
            'id'
        );

        $rules = [
            'send_to_all' => [
                'required',
                'boolean',
            ],
            'tags' => [
                'required_unless:send_to_all,1',
                'array',
                Rule::in($tags),
            ],
            'tags.*' => [
                'integer',
            ],
            'scheduled_at' => [
                'required',
                'date',
            ],
            'save_as_draft' => [
                'nullable',
                'boolean',
            ],
            'status_id' => 'valid_status',
        ];

        return array_merge($this->getRules(), $rules);
    }

    public function messages(): array
    {
        return [
            'valid_status' => __('A post cannot be updated if its status is not draft'),
            'tags.in' => 'One or more of the tags is invalid.',
        ];
    }
}

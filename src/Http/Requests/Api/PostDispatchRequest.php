<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Repositories\Posts\PostTenantRepositoryInterface;

class PostDispatchRequest extends FormRequest
{
    /**
     * @var PostTenantRepositoryInterface
     */
    protected $posts;

    /**
     * @var Post
     */
    protected $post;

    public function __construct(PostTenantRepositoryInterface $posts)
    {
        parent::__construct();

        $this->posts = $posts;

        Validator::extendImplicit('valid_status', function ($attribute, $value, $parameters, $validator) {
            return $this->getPost()->status_id === PostStatus::STATUS_DRAFT;
        });
    }

    /**
     * @param array $relations
     * @return Post
     * @throws \Exception
     */
    public function getPost(array $relations = []): Post
    {
        return $this->post = $this->posts->find(Targetforce::currentWorkspaceId(), $this->id, $relations);
    }

    public function rules()
    {
        return [
            'status_id' => 'valid_status',
        ];
    }

    public function messages(): array
    {
        return [
            'valid_status' => __('The post must have a status of draft to be dispatched'),
        ];
    }
}

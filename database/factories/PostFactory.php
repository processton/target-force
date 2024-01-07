<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\PostStatus;
use Targetforce\Base\Models\EmailService;
use Targetforce\Base\Models\Template;

class PostFactory extends Factory
{
    /** @var string */
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subject' => $this->faker->title,
            'from_name' => $this->faker->name,
            'from_email' => $this->faker->email,
            'email_service_id' => EmailService::factory(),
            'is_open_tracking' => true,
            'is_click_tracking' => true,
        ];
    }

    public function withContent(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'content' => $this->faker->paragraph,
            ];
        });
    }

    public function withTemplate(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'template_id' => Template::factory(),
            ];
        });
    }

    public function draft(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => PostStatus::STATUS_DRAFT,
            ];
        });
    }

    public function queued(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => PostStatus::STATUS_QUEUED,
            ];
        });
    }

    public function sending(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => PostStatus::STATUS_SENDING,
            ];
        });
    }

    public function sent(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => PostStatus::STATUS_SENT,
            ];
        });
    }

    public function cancelled(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => PostStatus::STATUS_CANCELLED,
            ];
        });
    }

    public function withoutOpenTracking(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_open_tracking' => false,
            ];
        });
    }

    public function withoutClickTracking(): PostFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_click_tracking' => false,
            ];
        });
    }
}

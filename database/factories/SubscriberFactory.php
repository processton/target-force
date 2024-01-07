<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Subscriber;

class SubscriberFactory extends Factory
{
    /** @var string */
    protected $model = Subscriber::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'hash' => $this->faker->uuid,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail
        ];
    }
}

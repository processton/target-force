<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Campaign;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Models\Subscriber;

class MessageFactory extends Factory
{
    /** @var string */
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'hash' => $this->faker->uuid,
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => Subscriber::factory(),
            'source_type' => Campaign::class,
            'source_id' => Campaign::factory(),
            'recipient_email' => $this->faker->email,
            'subject' => $this->faker->sentence(3),
            'from_name' => $this->faker->name,
            'from_email' => 'testing@targetforce.test',
            'message_id' => null,
            'ip' => $this->faker->ipv4,
            'open_count' => 0,
            'click_count' => 0,
            'queued_at' => null,
            'sent_at' => null,
            'delivered_at' => null,
            'bounced_at' => null,
            'unsubscribed_at' => null,
            'complained_at' => null,
            'opened_at' => null,
            'clicked_at' => null,
        ];
    }

    public function dispatched(): MessageFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'sent_at' => now(),
            ];
        });
    }

    public function pending(): MessageFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'sent_at' => null,
            ];
        });
    }
}

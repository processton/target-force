<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Template;

class TemplateFactory extends Factory
{
    /** @var string */
    protected $model = Template::class;
    
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'content' => '{{content}}'
        ];
    }
}

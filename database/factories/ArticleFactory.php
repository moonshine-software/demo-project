<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MoonShine\Permissions\Models\MoonshineUser;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucfirst($this->faker->words(2, true)),
            'author_id' => MoonshineUser::query()->inRandomOrder()->value('id'),
            'description' => $this->faker->text(),
            'slug' => $this->faker->slug(),
            'rating' => $this->faker->numberBetween(0,5),
            'link' => $this->faker->url(),
            'age_from' => $this->faker->numberBetween(0, 18),
            'age_to' => $this->faker->numberBetween(18, 60),
            'active' => $this->faker->boolean(),
            'color' => $this->faker->hexColor(),
            'files' => [],
            'data' => [],
            'code' => ''
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;


class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->name(),
            'title' => $this->faker->name(),
            'logo' => $this->faker->name(),
            'default_image' => $this->faker->name(),
            'slider' => $this->faker->name(),
            'description' => $this->faker->text(),
            'tags' => $this->faker->text(),
            'seo_keywords' => $this->faker->name(),
            'guarantee_time' => $this->faker->numberBetween(5.2,5.2),
            'send_time' => $this->faker->numberBetween(5.2,5.2),
            'parent_id' => 0,
            'status' => 1,
            'is_available' => 1,
            'is_physical' => 1,
            'seo_description' => $this->faker->text(),
        ];
    }
}

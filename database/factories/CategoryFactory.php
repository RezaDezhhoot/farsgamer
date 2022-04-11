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
            'logo' => 'https://png.pngtree.com/element_pic/00/16/09/2057e0eecf792fb.jpg',
            'default_image' => 'https://png.pngtree.com/element_pic/00/16/09/2057e0eecf792fb.jpg',
            'slider' => 'https://png.pngtree.com/element_pic/00/16/09/2057e0eecf792fb.jpg',
            'seo_keywords' => $this->faker->name(),
            'seo_description' => $this->faker->sentence(),
            'send_time' => $this->faker->numberBetween(1,5.2),
            'pay_time' => $this->faker->numberBetween(1,5.2),
            'receive_time' => $this->faker->numberBetween(1,5.2),
            'sending_data_time' => $this->faker->numberBetween(1,5.2),
            'no_receive_time' => $this->faker->numberBetween(1,5.2),
        ];
    }
}

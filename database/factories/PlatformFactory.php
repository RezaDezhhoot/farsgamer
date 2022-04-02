<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Platform::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->name(),
            'logo' => 'https://img.favpng.com/13/19/0/macintosh-apple-logo-scalable-vector-graphics-png-favpng-18KZGUdb1hwJJZyPnSHhzTRGu.jpg',
        ];
    }
}

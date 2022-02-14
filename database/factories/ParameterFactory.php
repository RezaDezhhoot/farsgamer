<?php

namespace Database\Factories;

use App\Models\Parameter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParameterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Parameter::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => rand(1,5),
            'logo' => 'admin/build/images/img.jpg',
            'name' => $this->faker->name(),
            'type' => 'number',
            'field' => $this->faker->name(),
            'status' => 'available',
            'max' => 1000,
            'min' => 20
        ];
    }
}

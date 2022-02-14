<?php

namespace Database\Factories;

use App\Models\OrderResult;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderResult::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id' => 1,
            'message' => $this->faker->text(),
            'status' => 'new',
            'is_read' => 0
        ];
    }
}

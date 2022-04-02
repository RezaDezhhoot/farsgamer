<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->name(),
            'content' => 'test',
            'price' => 1000.2,
            'image' => 'https://png.pngtree.com/element_pic/00/16/09/2057e0eecf792fb.jpg',
            'status' => Order::IS_CONFIRMED,
            'gallery' => 'https://png.pngtree.com/element_pic/00/16/09/2057e0eecf792fb.jpg,https://png.pngtree.com/element_pic/00/16/09/2057e0eecf792fb.jpg',
        ];
    }
}

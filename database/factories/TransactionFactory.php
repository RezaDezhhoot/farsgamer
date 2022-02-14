<?php

namespace Database\Factories;

use App\Models\OrderTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id' => 2,
            'seller_id' => 1,
            'order_id' => 2,
            'status' => 'canceled',
        ];
    }
}

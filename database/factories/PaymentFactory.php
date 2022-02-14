<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'user_ip' => 12,
            'json' => 4,
            'price' => 15.5,
            'new' => 1,
            'track_id' => 55,
            'gateway' => 'test',
            'status' => 'test',
            'transaction_id' => 1,
            'payment_for' => 'transaction',
            'case_id' => 1
        ];
    }
}

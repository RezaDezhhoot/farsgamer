<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'user_name' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'province' => $this->faker->name(),
            'city' => $this->faker->name(),
            'phone' => $this->faker->randomNumber(),
            'code_id' => Str::random(10),
            'token' => Str::random(10),
            'pass_word' => Hash::make('test'),
            'description' => 'test',
            'ip' => 'test',
            'score' => 0,
            'rate' => 0,
            'order-transactions' => 0,
            'orders' => 0,
            'remember_token' => Str::random(10),

        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

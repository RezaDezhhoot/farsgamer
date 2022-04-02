<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create();
        Category::factory()->count(10)->create()->each(function ($category) use ($user){
            Order::factory()->count(rand(6,20))->make(['user_id' => $user->id])->each(function ($order,$key) use ($category){
                $category->orders()->save($order);
            });
        });
    }
}

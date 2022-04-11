<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return string
     */
    public function run()
    {
        try {
            DB::beginTransaction();
            $user = User::factory()->create();
            Category::factory()->count(10)->create()->each(function ($category) use ($user){
                Order::factory()->count(rand(6,20))->make(['user_id' => $user->id])->each(function ($order) use ($category){
                    $category->orders()->save($order);
                });
            });
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return true;
    }
}

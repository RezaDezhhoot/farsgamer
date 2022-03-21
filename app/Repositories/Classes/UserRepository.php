<?php


namespace App\Repositories\Classes;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getUser($col, $value)
    {
        return User::where($col,$value)->first();
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function update(User $user, array $data)
    {
        $user->update($data);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function getStatus()
    {
        User::getStatus();
    }

    public function getMyOrders(User $user ,$active = true)
    {
        return $user->orders()->active($active)->get();
    }

    public function getMyComments(User $user, $active = true)
    {
        return $user->comments()->active($active)->get();
    }
}

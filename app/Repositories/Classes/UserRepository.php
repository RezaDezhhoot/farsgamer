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

    public function hasRole($role)
    {
        return auth()->user()->hasRole($role);
    }

    public function getUserNotifications(User $user , $subject = null , $model_id = null)
    {
        if (!is_null($subject))
            return $user->alerts()->where('subject' , $subject)->get();
        elseif (!is_null($subject) && !is_null($model_id))
            return $user->alerts()->where([
                ['subject' , $subject],
                ['model_id' , $model_id],
            ])->get();

        return  $user->alerts;
    }

    /**
     * @return mixed
     */
    public function newStatus()
    {
        return User::NEW;
    }
}

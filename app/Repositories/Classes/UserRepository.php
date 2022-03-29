<?php


namespace App\Repositories\Classes;

use App\Models\Notification;
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
        return User::getStatus();
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

    public function getUsersNotifications(array $users, $subject = null, $model_id = null)
    {
        // TODO: Implement getUsersNotifications() method.
        if (!is_null($subject)){
            return Notification::where([
                ['subject' , $subject],
            ])->where(function ($query) use ($users) {
                return $query->where('user_id',$users[0]->id)->orWhere('user_id',$users[1]->id);
            })->get();
        }
        elseif (!is_null($subject) && !is_null($model_id))
            return Notification::where([
                ['subject' , $subject],
                ['model_id' , $model_id],
            ])->where(function ($query) use ($users) {
                return $query->where('user_id',$users[0]->id)->orWhere('user_id',$users[1]->id);
            })->get();
    }

    /**
     * @return mixed
     */
    public function newStatus()
    {
        return User::NEW;
    }


    /**
     * @param $col
     * @param string $desk
     * @return mixed
     */
    public function getUserOrderBy($col,$desk = 'desc')
    {
        return User::orderBy($col , $desk);
    }

    /**
     * @param $value
     * @param $key
     * @param string $col
     * @param string $desk
     * @return mixed
     */
    public function pluck($value, $key , $col = 'id' , $desk = 'desc')
    {
        return $this->getUserOrderBy($col,$desk)->pluck($value,$key);
    }

    public function save(User $user)
    {
        $user->save();
        return $user;
    }

    public function getAllAdminList($status, $roles, $search, $pagination)
    {
        return User::latest('id')->when($status, function ($query) use ($status) {
            return $query->where('status' , $status);
        })->when($roles, function ($query) use ($roles) {
            return $query->role($roles);
        })->search($search)->paginate($pagination);
    }

    public static function confirmedStatus()
    {
        return User::CONFIRMED;
    }

    public static function notConfirmedStatus()
    {
        // TODO: Implement unconfirmedStatus() method.
        return User::NOT_CONFIRMED;
    }

    public function newUserObject()
    {
        // TODO: Implement newUserObject() method.
        return new User();
    }

    public function syncRoles(User $user ,$roles)
    {
        // TODO: Implement syncRoles() method.
        return $user->syncRoles($roles);
    }

    public function getUserCards(User $user)
    {
        // TODO: Implement getUserCards() method.
        return $user->cards;
    }

    public function walletTransactions(User $user)
    {
        // TODO: Implement walletTransactions() method.
        return $user->walletTransactions()->where('confirmed', 1)->get();
    }

    public function getUserOvertimes(User $user)
    {
        // TODO: Implement getUserOvertimes() method.
        return $user->overtimes;
    }

    public static function getNew()
    {
        // TODO: Implement getNew() method.
        return User::getNew();
    }

    public function authenticated()
    {
        // TODO: Implement authenticated() method.
        return auth()->user()->status == self::confirmedStatus();
    }

    public static function waitToConfirmStatus()
    {
        // TODO: Implement waitToConfirmStatus() method.
        return User::WAIT_TO_CONFIRM;
    }

    public function waiting()
    {
        // TODO: Implement waiting() method.
        return auth()->user()->status == self::waitToConfirmStatus();
    }

    public function getUserCard(User $user, $id)
    {
        return $user->cards()->findOrFail($id);
    }

    public function getLastNotifications(User $user, $count)
    {
        return $user->alerts()->latest('id')->take($count)->get();
    }
}

<?php


namespace App\Repositories\Classes;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function cardStatus()
    {
        return Notification::CARD;
    }

    public function getSubjects()
    {
        return Notification::getSubject();
    }

    public function create(array $data)
    {
        return Notification::create($data);
    }

    public function privateType()
    {
        return Notification::PRIVATE;
    }

    public function publicType()
    {
        return Notification::PUBLIC;
    }

    /**
     * @return mixed
     */
    public function requestStatus()
    {
        return Notification::REQUEST;
    }

    /**
     * @return mixed
     */
    public static function getType()
    {
        return Notification::getType();
    }

    /**
     * @param $search
     * @param $type
     * @param $subject
     * @param $pagination
     * @return mixed
     */
    public function getAllAdminList($search, $type, $subject, $pagination)
    {
        return Notification::with(['user'])->latest('id')->when($search, function ($query) use ($search) {
            return $query->whereHas('user', function ($query) use ($search) {
                return is_numeric($search) ?
                    $query->where('phone',$search) : $query->where('user_name',$search);
            });
        })->when($type,function ($query) use ($type){
            return $query->where('type',$type);
        })->when($subject,function ($query) use ($subject){
            return $query->where('subject',$subject);
        })->paginate($pagination);
    }


    /**
     * @param Notification $notification
     * @return mixed
     */
    public function delete(Notification $notification)
    {
        return $notification->delete();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Notification::findOrFail($id);
    }

    public function orderStatus()
    {
        return Notification::ORDER;
    }

    public function authStatus()
    {
        // TODO: Implement authStatus() method.
        return Notification::AUTH;
    }

    public function userStatus()
    {
        // TODO: Implement userStatus() method.
        return Notification::User;
    }

    public function transactionStatus()
    {
        // TODO: Implement transactionStatus() method.
        return Notification::TRANSACTION;
    }
}

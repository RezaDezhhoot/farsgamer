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
}

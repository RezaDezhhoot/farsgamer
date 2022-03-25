<?php
namespace App\Repositories\Interfaces;
use App\Models\Notification;
use Illuminate\Http\Request;

interface NotificationRepositoryInterface
{
    public function cardStatus();

    public function orderStatus();

    public function requestStatus();

    public function getSubjects();

    public function create(array $data);

    public function privateType();

    public function publicType();

    public static function getType();

    public function getAllAdminList($search , $type , $subject , $pagination);

    public function delete(Notification $notification);

    public function find($id);

    public function authStatus();

    public function userStatus();

    public function transactionStatus();
}

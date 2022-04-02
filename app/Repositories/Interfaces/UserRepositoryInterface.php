<?php
namespace App\Repositories\Interfaces;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getUser($col,$value);

    public function find($id);

    public function create(array $data);

    public function update(User $user, array $data);

    public function getStatus();

    public function getMyOrders(User $user , $active = true);

    public function getMyComments(User $user , $active = true);

    public function hasRole($role);

    public function getUserNotifications(User $user , $subject = null , $model_id = null);

    public function getUsersNotifications(array $users , $subject = null , $model_id = null);

    public function newStatus();

    public function getUserOrderBy($col , $desk = 'desc');

    public function pluck($value,$key , $col = 'id' , $desk = 'desc');

    public function save(User $user);

    public function getAllAdminList($status , $roles , $search , $pagination);

    public static function confirmedStatus();

    public static function notConfirmedStatus();

    public static function waitToConfirmStatus();

    public function newUserObject();

    public function syncRoles(User $user ,$roles);

    public function getUserCards(User $user);

    public function getUserCard(User $user,$id);

    public function walletTransactions(User $user);

    public function getUserOvertimes(User $user);

    public static function getNew();

    public function authenticated();

    public function waiting();

    public function getLastNotifications(User $user , $count);

    public function registerComment(User $user , array $data );
}

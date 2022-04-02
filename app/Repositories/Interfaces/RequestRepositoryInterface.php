<?php
namespace App\Repositories\Interfaces;


use App\Models\Request;
use App\Models\User;

interface RequestRepositoryInterface
{
    public function getAllAdminList($status , $search , $phone , $user_name ,$pagination);

    public static function getStatus();

    public function getByConditionCount($col , $operator , $value );

    public function find($id);

    public function save(Request $request);

    public static function newStatus();

    public static function rejectedStatus();

    public static function settlementStatus();

    public static function getNew();

    public function getUserRequests(User $user);

    public function getUserRequest(User $user , $id);

    public function create(User $user , array $data);
}

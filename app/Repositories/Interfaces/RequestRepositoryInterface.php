<?php
namespace App\Repositories\Interfaces;


use App\Models\Request;

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
}

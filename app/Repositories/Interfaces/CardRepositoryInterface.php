<?php
namespace App\Repositories\Interfaces;


use App\Models\Card;

interface CardRepositoryInterface
{
    public function getAllAdminList($search , $status , $pagination , $active = true);

    public function getStatus();

    public function getByCondition($col , $operator , $value , $active = true);

    public function getByConditionCount($col , $operator , $value , $active = true);

    public function delete(Card $card);

    public function find($id,$active = true);

    public function getBank();

    public function save(Card $card);

    public static function confirmStatus();

    public static function getNew();
}

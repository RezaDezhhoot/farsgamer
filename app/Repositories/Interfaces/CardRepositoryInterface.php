<?php
namespace App\Repositories\Interfaces;


use App\Models\Card;
use App\Models\User;

interface CardRepositoryInterface
{
    public function getAllAdminList($search , $status , $pagination , $active = true);

    public function getStatus();

    public function getByCondition($col , $operator , $value , $active = true);

    public function getByConditionCount($col , $operator , $value , $active = true);

    public function delete(Card $card);

    public function destroy($id);

    public function find($id,$active = true);

    public function getBank();

    public function save(Card $card);

    public static function confirmStatus();

    public static function newStatus();

    public static function getNew();

    public function create(array $data);

    public function updateOrCreate(array $key , array $value);

    public function getFirst($user);

    public function update(Card $card , array $data);
}

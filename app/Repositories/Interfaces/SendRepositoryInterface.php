<?php
namespace App\Repositories\Interfaces;


use App\Models\Send;

interface SendRepositoryInterface
{
    public function getByCondition($col , $operator , $value , $active = true);

    public function availableStatus();

    public function getAllAdminList($status , $search , $pagination);

    public function getStatus();

    public function find($id);

    public function delete(Send $send);

    public function newSendObject();

    public function save(Send $send);
}

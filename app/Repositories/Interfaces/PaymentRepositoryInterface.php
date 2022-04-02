<?php

namespace App\Repositories\Interfaces;


use App\Models\Payment;
use App\Models\User;

interface PaymentRepositoryInterface
{
    public function getAllAdminList($ip , $user,$status,$search , $pagination);

    public static function getStatus();

    public function find($id);

    public function delete(Payment $payment);

    public function create(User $user , array $data);

    public function whereCause(array $where);

    public function newObject();
}

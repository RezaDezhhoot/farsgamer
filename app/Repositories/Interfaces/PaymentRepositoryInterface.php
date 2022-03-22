<?php

namespace App\Repositories\Interfaces;


use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function getAllAdminList($ip , $user,$status,$search , $pagination);

    public static function getStatus();

    public function find($id);

    public function delete(Payment $payment);
}

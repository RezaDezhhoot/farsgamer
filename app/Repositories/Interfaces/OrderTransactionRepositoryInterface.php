<?php
namespace App\Repositories\Interfaces;

interface OrderTransactionRepositoryInterface
{
    public function start($order,$commission);
}

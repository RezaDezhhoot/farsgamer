<?php


namespace App\Repositories\Classes;

use App\Models\Send;
use App\Repositories\Interfaces\SendRepositoryInterface;

class SendRepository implements SendRepositoryInterface
{

    public function getByCondition($col, $operator, $value, $active = true)
    {
        return Send::active($active)->where($col,$operator,$value)->get();
    }

    public function availableStatus()
    {
        return Send::AVAILABLE;
    }
}

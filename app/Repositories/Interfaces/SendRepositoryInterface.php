<?php
namespace App\Repositories\Interfaces;


interface SendRepositoryInterface
{
    public function getByCondition($col , $operator , $value , $active = true);

    public function availableStatus();
}

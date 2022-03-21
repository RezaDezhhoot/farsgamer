<?php


namespace App\Repositories\Classes;

use App\Models\Offend;
use App\Repositories\Interfaces\OffendRepositoryInterface;

class OffendRepository implements OffendRepositoryInterface
{

    public function create(array $data)
    {
        return Offend::create($data);
    }
}

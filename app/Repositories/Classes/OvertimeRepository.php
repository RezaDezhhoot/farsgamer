<?php


namespace App\Repositories\Classes;

use App\Models\Overtime;
use App\Models\Platform;
use App\Repositories\Interfaces\OvertimeRepositoryInterface;


class OvertimeRepository implements OvertimeRepositoryInterface
{

    public function find($id)
    {
        // TODO: Implement find() method.
        return Overtime::findOrfail($id);
    }

    public function delete(Overtime $overtime)
    {
        // TODO: Implement delete() method.
        return $overtime->delete();
    }

    public function create(array $data)
    {
        return Overtime::create($data);
    }
}

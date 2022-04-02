<?php


namespace App\Repositories\Classes;

use App\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;


class PermissionRepository implements PermissionRepositoryInterface
{

    public function getAll()
    {
        return Permission::all();
    }

    public function insert(array $data)
    {
        // TODO: Implement insert() method.
        return Permission::insert($data);
    }
}

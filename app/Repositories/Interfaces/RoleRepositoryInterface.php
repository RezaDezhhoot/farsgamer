<?php

namespace App\Repositories\Interfaces;

use App\Models\Role;

interface RoleRepositoryInterface
{
    public function getAllAdminList($search , $pagination);

    public function whereNotIn($name,array $values , $find = null);

    public function find($id);

    public function newRoleObject();

    public function syncPermissions(Role $role ,$permissions);

    public function save(Role $role);

    public function delete(Role $role);

    public function create(array $data);
}

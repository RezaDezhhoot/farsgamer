<?php


namespace App\Repositories\Classes;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;


class RoleRepository implements RoleRepositoryInterface
{

    public function getAllAdminList($search, $pagination)
    {
        return Role::latest('id')->whereNotIn('name', ['administrator', 'super_admin', 'admin'])
            ->search($search)->paginate($pagination);
    }


    public function whereNotIn($name, array $values , $find = null)
    {
        return !is_null($find) ? Role::whereNotIn($name, $values)->findOrFail($find) : Role::whereNotIn($name, $values)->get();
    }

    public function find($id)
    {
        return Role::findOrFail($id);
    }

    public function newRoleObject()
    {
        return new Role();
    }

    public function syncPermissions(Role $role, $permissions)
    {
        return $role->syncPermissions($permissions);
    }


    public function save(Role $role)
    {
        $role->save();
        return $role;
    }

    public function delete(Role $role)
    {
        return $role->delete();
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
        return Role::create($data);
    }
}

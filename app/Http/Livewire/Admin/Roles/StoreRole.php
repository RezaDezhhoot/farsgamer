<?php

namespace App\Http\Livewire\Admin\Roles;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class StoreRole extends BaseComponent
{
    public $role , $permission , $name  , $header , $mode , $permissionSelected = [];
    public function mount(RoleRepositoryInterface $roleRepository , PermissionRepositoryInterface $permissionRepository, $action , $id = null)
    {
        $this->authorizing('show_roles');
        if ($action == 'edit')
        {
            $this->role = $roleRepository->whereNotIn('name', ['administrator', 'super_admin', 'admin'],$id);
            $this->header = $this->role->name;
            $this->name = $this->role->name;
            $this->permissionSelected = $this->role->permissions()->pluck('name')->toArray();
        } elseif($action == 'create') $this->header = 'نقش جدید';
        else abort(404);

        $this->mode = $action;
        $this->permission = $permissionRepository->getAll();
    }

    public function store(RoleRepositoryInterface $roleRepository)
    {
        $this->authorizing('edit_roles');
        if ($this->mode == 'edit')
            $this->saveInDateBase($roleRepository,$this->role);
        else {
            $this->saveInDateBase($roleRepository,$roleRepository->newRoleObject());
            $this->reset(['name','permissionSelected']);
        }
    }

    public function saveInDateBase($roleRepository ,  $model)
    {
        $this->validate(
            [
                'name' => ['required', 'string','max:250'],
                'permissionSelected' => ['required', 'array'],
                'permissionSelected.*' => ['required', 'exists:permissions,name'],
            ] , [] , [
                'name' => 'عنوان',
                'permissionSelected' => 'دسترسی ها',
                'permissionSelected.*' => 'دسترسی ها',
            ]
        );
        $model->name = $this->name;
        $model = $roleRepository->save($model);
        $roleRepository->syncPermissions($model, $this->permissionSelected);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem(RoleRepositoryInterface $roleRepository)
    {
        $this->authorizing('delete_roles');
        $role = $roleRepository->whereNotIn('name', ['administrator', 'super_admin', 'admin'] , $this->role->id);
        $roleRepository->delete($role);
        return redirect()->route('admin.role');
    }

    public function render()
    {
        return view('livewire.admin.roles.store-role')->extends('livewire.admin.layouts.admin');
    }
}

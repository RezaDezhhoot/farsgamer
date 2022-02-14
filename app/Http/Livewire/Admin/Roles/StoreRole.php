<?php

namespace App\Http\Livewire\Admin\Roles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StoreRole extends Component
{
    use AuthorizesRequests;
    public $role , $permission , $name  , $header , $mode , $permissionSelected = [];
    public function mount($action , $id = null)
    {
        $this->authorize('show_roles');
        if ($action == 'edit')
        {
            $this->role = Role::whereNotIn('name', ['administrator', 'super_admin', 'admin'])->findOrFail($id);
            $this->header = $this->role->name;
            $this->name = $this->role->name;
            $this->permissionSelected = $this->role->permissions()->pluck('name')->toArray();
        } elseif($action == 'create') $this->header = 'نقش جدید';
        else abort(404);

        $this->mode = $action;
        $this->permission = Permission::all();
    }

    public function store()
    {
        $this->authorize('edit_roles');
        if ($this->mode == 'edit')
            $this->saveInDateBase($this->role);
        else {
            $this->saveInDateBase(new Role());
            $this->reset(['name','permissionSelected']);
        }
    }

    public function saveInDateBase(Role $model)
    {
        $this->validate(
            [
                'name' => ['required', 'string','max:250'],
                'permissionSelected' => ['required', 'array'],
                'permissionSelected.*' => ['required', 'exists:permissions,name'],
            ] , [] , [
                'name' => 'عنوان',
                'permissionSelected' => '',
                'permissionSelected.*' => '',
            ]
        );
        $model->name = $this->name;
        $model->save();
        $model->syncPermissions($this->permissionSelected);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem()
    {
        $this->authorize('delete_roles');
        Role::whereNotIn('name', ['administrator', 'super_admin', 'admin'])->findOrFail($this->role->id)->delete();
        return redirect()->route('admin.role');
    }

    public function render()
    {
        return view('livewire.admin.roles.store-role')->extends('livewire.admin.layouts.admin');
    }
}

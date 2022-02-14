<?php

namespace App\Http\Livewire\Admin\Roles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;

class IndexRole extends Component
{
    use WithPagination , AuthorizesRequests;
    public $pagination = 10 , $search , $placeholder = 'عنوان';

    public function render()
    {
        $this->authorize('show_roles');
        $roles = Role::latest('id')->whereNotIn('name', ['administrator', 'super_admin', 'admin'])
            ->search($this->search)->paginate($this->pagination);
        return view('livewire.admin.roles.index-role',['roles' => $roles])->extends('livewire.admin.layouts.admin');
    }
}

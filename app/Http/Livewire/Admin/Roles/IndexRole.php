<?php

namespace App\Http\Livewire\Admin\Roles;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Livewire\WithPagination;

class IndexRole extends BaseComponent
{
    use WithPagination ;
    public $placeholder = 'عنوان';

    public function render(RoleRepositoryInterface $roleRepository)
    {
        $this->authorizing('show_roles');
        $roles = $roleRepository->getAllAdminList($this->search , $this->pagination);
        return view('livewire.admin.roles.index-role',['roles' => $roles])
            ->extends('livewire.admin.layouts.admin');
    }
}

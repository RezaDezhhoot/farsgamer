<?php

namespace App\Http\Livewire\Admin\Users;

use App\Http\Livewire\BaseComponent;
use App\Models\Notification;
use App\Models\Role;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use Livewire\WithPagination;

class IndexUser extends BaseComponent
{
    use WithPagination , AuthorizesRequests  , TextBuilder;
    public $pagination = 10 , $search , $roles , $data , $status , $placeholder = 'نام کاربری یا شماره همراه';

    protected $queryString = ['status','roles'];

    public function render()
    {
        $this->authorize('show_users');
        $this->data['status'] = User::getStatus();
        $this->data['roles'] = Role::whereNotIn('name', ['administrator', 'super_admin', 'admin'])->pluck('name','name');
        $users = User::latest('id')->when($this->status, function ($query) {
            return $query->where('status' , $this->status);
        })->when($this->roles, function ($query) {
            return $query->role($this->roles);
        })->search($this->search)->paginate($this->pagination);
        return view('livewire.admin.users.index-user',['users' => $users])->extends('livewire.admin.layouts.admin');
    }

    public function confirm($id)
    {
        $this->authorize('edit_users');
        $user = User::findOrFail($id);
        if ($user->status <> User::CONFIRMED) {
            $user->status = User::CONFIRMED;
            $user->save();
            $text = $this->createText('auth',$user);
            $send = new SendMessages();
            $send->sends($text,$user,Notification::AUTH);
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        }
    }
}

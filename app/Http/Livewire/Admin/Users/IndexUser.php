<?php

namespace App\Http\Livewire\Admin\Users;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Livewire\WithPagination;

class IndexUser extends BaseComponent
{
    use WithPagination  , TextBuilder;
    public $roles , $data , $status , $placeholder = 'نام کاربری یا شماره همراه';

    protected $queryString = ['status','roles'];

    public function render(UserRepositoryInterface $userRepository , RoleRepositoryInterface $roleRepository)
    {
        $this->authorizing('show_users');
        $this->data['status'] = $userRepository->getStatus();
        $this->data['roles'] = $roleRepository->whereNotIn('name', ['administrator', 'super_admin', 'admin'])->pluck('name','name');
        $users = $userRepository->getAllAdminList($this->status,$this->roles ,$this->search,$this->pagination);
        return view('livewire.admin.users.index-user',['users' => $users])->extends('livewire.admin.layouts.admin');
    }

    public function confirm(UserRepositoryInterface $userRepository ,NotificationRepositoryInterface $notificationRepository ,$id)
    {
        $this->authorizing('edit_users');
        $user = $userRepository->find($id);
        if ($user->status <> $userRepository::confirmedStatus()) {
            $user->status = $userRepository::confirmedStatus();
            $userRepository->save($user);
            $text = $this->createText('auth',$user);
            $send = new SendMessages();
            $send->sends($text,$user,$notificationRepository->authStatus());
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        }
    }
}

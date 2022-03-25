<?php

namespace App\Http\Livewire\Admin\Notifications;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Livewire\WithPagination;


class IndexNotification extends BaseComponent
{
    use WithPagination ;
    public $subject , $type, $data = [] , $placeholder = 'نام کاربری یا شماره همراه کاربری';
    protected $queryString = ['type','subject'];
    public function render(NotificationRepositoryInterface $notificationRepository)
    {
        $this->authorizing('show_notifications');
        $notification = $notificationRepository->getAllAdminList($this->search,$this->type,$this->subject,$this->pagination);
        $this->data['type'] = $notificationRepository::getType();
        $this->data['subject'] = $notificationRepository->getSubjects();
        return view('livewire.admin.notifications.index-notification',['notification' => $notification])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , NotificationRepositoryInterface $notificationRepository)
    {
        $this->authorizing('delete_notifications');
        $notification = $notificationRepository->find($id);
        $notificationRepository->delete($notification);
    }
}

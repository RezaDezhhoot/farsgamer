<?php

namespace App\Http\Livewire\Admin\Notifications;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreNotification extends BaseComponent
{
    use AuthorizesRequests;
    public $header , $mode , $content , $type , $subject , $user_id , $data = [];
    public function mount(NotificationRepositoryInterface $notificationRepository,UserRepositoryInterface $userRepository,$action , $id = null)
    {
        $this->authorize('show_notifications');
        if ($action == 'create') {
            $this->header = 'اعلان جدید';
            $this->mode = $action;
            $this->data['type'] = $notificationRepository::getType();
            $this->data['subject'] = $notificationRepository->getSubjects();
            $this->data['user'] = $userRepository->pluck('user_name','id','user_name');
        } else abort(404);
    }

    public function store(NotificationRepositoryInterface $notificationRepository)
    {
        $this->authorize('edit_notifications');
        $filed = [
            'subject' => ['required', 'string','in:'.implode(',',array_keys($this->data['subject']))],
            'content' => ['required', 'string','max:250'],
            'type' => ['required','string' ,'in:'.implode(',',array_keys($notificationRepository::getType()))],
        ];
        $message = [
            'subject' => 'موضوع',
            'content' => 'متن',
            'type' => 'نوع اعلان',
        ];

        if (isset($this->user_id) || $this->type == $notificationRepository->privateType()) {
            $filed['user_id'] = ['required','exists:users,id'];
            $message['user_id'] = 'کاربر';
        }
        $this->validate($filed,[],$message);
        $notification = [
            'subject' => $this->subject,
            'content' =>  $this->content,
            'type' => $this->type,
            'model' => $this->subject,
            'model_id' => null
        ];
        if ($this->type == $notificationRepository->privateType())
            $notification['user_id'] = $this->user_id;
        else
            $notification['user_id'] = null;

        $notificationRepository->create($notification);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        $this->reset(['content','type','user_id','subject']);
    }


    public function render()
    {
        return view('livewire.admin.notifications.store-notification')
            ->extends('livewire.admin.layouts.admin');
    }
}

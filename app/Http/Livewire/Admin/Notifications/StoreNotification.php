<?php

namespace App\Http\Livewire\Admin\Notifications;

use App\Http\Livewire\BaseComponent;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Notification;

class StoreNotification extends BaseComponent
{
    use AuthorizesRequests;
    public $header , $mode , $content , $type , $subject , $user_id , $data = [];
    public function mount($action , $id = null)
    {
        $this->authorize('show_notifications');
        if ($action == 'create') {
            $this->header = 'اعلان جدید';
            $this->mode = $action;
            $this->data['type'] = Notification::getType();
            $this->data['subject'] = Notification::getSubject();
            $this->data['user'] = User::orderBy('user_name')->pluck('user_name','id');
        } else abort(404);
    }

    public function store()
    {
        $this->authorize('edit_notifications');
        $filed = [
            'subject' => ['required', 'string','in:'.implode(',',array_keys($this->data['subject']))],
            'content' => ['required', 'string','max:250'],
            'type' => ['required','string' ,'in:'.Notification::PUBLIC.','.Notification::PRIVATE],
        ];
        $message = [
            'subject' => 'موضوع',
            'content' => 'متن',
            'type' => 'نوع اعلان',
        ];

        if (isset($this->user_id) || $this->type == Notification::PRIVATE) {
            $filed['user_id'] = ['required','exists:users,id'];
            $message['user_id'] = 'کاربر';
        }
        $this->validate($filed,[],$message);
        $notification = new Notification();
        $notification->subject = $this->subject;
        $notification->model = $this->subject;
        $notification->model_id = null;
        $notification->content = $this->content;
        $notification->type = $this->type;
        if ($this->type == Notification::PRIVATE)
            $notification->user_id = $this->user_id;
        else
            $notification->user_id = null;

        $notification->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        $this->reset(['content','type','user_id']);
    }


    public function render()
    {
        return view('livewire.admin.notifications.store-notification')->extends('livewire.admin.layouts.admin');
    }
}

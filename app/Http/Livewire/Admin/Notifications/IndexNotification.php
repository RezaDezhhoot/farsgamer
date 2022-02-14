<?php

namespace App\Http\Livewire\Admin\Notifications;

use App\Http\Livewire\BaseComponent;
use App\Models\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;


class IndexNotification extends BaseComponent
{
    use WithPagination , AuthorizesRequests;
    public $pagination = 10 , $search,$subject , $type, $data = [] , $placeholder = 'نام کاربری یا شماره همراه کاربری';
    protected $queryString = ['type','subject'];
    public function render()
    {
        $this->authorize('show_notifications');
        $notification = Notification::with(['user'])->latest('id')->when($this->search, function ($query) {
            return $query->whereHas('user', function ($query) {
                return is_numeric($this->search) ?
                    $query->where('phone', $this->search) : $query->where('user_name', $this->search);
            });
        })->when($this->type,function ($query){
            return $query->where('type',$this->type);
        })->when($this->subject,function ($query){
            return $query->where('subject',$this->subject);
        })->paginate($this->pagination);
        $this->data['type'] = Notification::getType();
        $this->data['subject'] = Notification::getSubject();
        return view('livewire.admin.notifications.index-notification',['notification' => $notification])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_notifications');
        Notification::findOrFail($id)->delete();
    }
}

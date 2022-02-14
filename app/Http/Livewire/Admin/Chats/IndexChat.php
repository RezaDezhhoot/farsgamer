<?php

namespace App\Http\Livewire\Admin\Chats;

use App\Http\Livewire\BaseComponent;
use App\Models\Chat;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\ChatGroup;

class IndexChat extends BaseComponent
{
    use AuthorizesRequests , WithPagination;
    public $placeholder = ' شماره همراه یا نام کاربری کاربر' , $pagination = 10 , $search , $data = [] , $chatList = [] , $chatText;

    public function render()
    {
        $this->authorize('show_chats');
        $groups = ChatGroup::latest('id')->with(['user_one','user_two','chats'])
            ->when($this->search,function ($query){
                return $query->whereHas('user_one',function ($query){
               return is_numeric($this->search)
                   ? $query->where('phone',$this->search) : $query->where('user_name',$this->search);
            })->orWhereHas('user_two',function ($query){
                return is_numeric($this->search)
                    ? $query->where('phone',$this->search) : $query->where('user_name',$this->search);
            })->orWhere('slug',$this->search);
        })->whereColumn('user1', '!=' ,'user2')->get();
        $this->data['status'] = ChatGroup::getStatus();
        return view('livewire.admin.chats.index-chat',['groups'=>$groups])->extends('livewire.admin.layouts.admin');
    }


    public function openChatList($id)
    {
        $this->authorize('show_chats');
        $group = ChatGroup::findOrFail($id);
        $this->chatList = $group;
    }

    public function sendChatText()
    {
        $this->authorize('edit_chats');
        if (!empty($this->chatList )  && !empty(preg_replace('/\s+/', '', $this->chatText)) && !is_null(trim($this->chatText))) {
            $chat = new Chat();
            $chat->sender_id = \auth()->id();
            $chat->receiver_id = $this->chatList->user1;
            $chat->content = $this->chatText;
            $chat->is_admin = auth()->user()->hasRole('admin');
            $chat->group_id = $this->chatList->id;
            $chat->save();
            $this->chatList = ChatGroup::findOrFail($this->chatList->id);
            $this->reset(['chatText']);
        }
    }


    public function blockChat()
    {
        $this->authorize('edit_chats');
        if (!empty($this->chatList )){
            $this->chatList->status = ChatGroup::CLOSE;
            $this->chatList->save();
        }
    }

    public function unBlockChat()
    {
        $this->authorize('edit_chats');
        if (!empty($this->chatList )){
            $this->chatList->status = ChatGroup::OPEN;
            $this->chatList->save();
        }
    }

    public function deleteChat()
    {
        $this->authorize('delete_chats');
        if (!empty($this->chatList )){
            $this->chatList->delete();
            $this->reset(['chatList']);
        }
    }

}

<?php

namespace App\Http\Livewire\Admin\MyChats;

use App\Http\Livewire\BaseComponent;
use App\Models\Chat;

class IndexMyChats extends BaseComponent
{
    public $chatList = [] , $chatText;
    public function render()
    {
        $groups = auth()->user()->contacts()->get();
        return view('livewire.admin.my-chats.index-my-chats',['groups'=>$groups])
            ->extends('livewire.admin.layouts.admin');
    }

    public function openChatList($id)
    {
        $group = auth()->user()->contacts()->findOrFail($id);
        $this->chatList = $group;
    }

    public function sendChatText()
    {
        if (!empty($this->chatList )  && !empty(preg_replace('/\s+/', '', $this->chatText)) && !is_null(trim($this->chatText))) {
            $chat = new Chat();
            $chat->sender_id = \auth()->id();
            $chat->receiver_id = $this->chatList->user1 == \auth()->id() ? $this->chatList->user2 : $this->chatList->user1;;
            $chat->content = $this->chatText;
            $chat->is_admin = auth()->user()->hasRole('admin');
            $chat->group_id = $this->chatList->id;
            $chat->save();
            $this->chatList = auth()->user()->contacts()->findOrFail($this->chatList->id);
            $this->reset(['chatText']);
        }
    }
}

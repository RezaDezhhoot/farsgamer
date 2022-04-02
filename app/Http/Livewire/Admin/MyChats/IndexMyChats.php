<?php

namespace App\Http\Livewire\Admin\MyChats;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Traits\Admin\ChatList;

class IndexMyChats extends BaseComponent
{
    use ChatList;
    public $chatList , $chatText;
    public function render(ChatRepositoryInterface $chatRepository)
    {
        $groups = $chatRepository->getContacts();
        return view('livewire.admin.my-chats.index-my-chats',['groups'=>$groups])
            ->extends('livewire.admin.layouts.admin');
    }

    public function openChatList($id , ChatRepositoryInterface $chatRepository)
    {
        $this->chatList = $chatRepository->find($id);;
        $this->chats = $this->chatList;
        $this->chatUserId = $this->chatList->user1 == auth()->id() ? $this->chatList->user2 : $this->chatList->user1;
    }
}

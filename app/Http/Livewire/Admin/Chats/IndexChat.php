<?php

namespace App\Http\Livewire\Admin\Chats;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Traits\Admin\ChatList;
use Livewire\WithPagination;

class IndexChat extends BaseComponent
{
    use WithPagination , ChatList;
    public $placeholder = ' شماره همراه یا نام کاربری کاربر'  , $data = [] , $chatList , $chatText;

    public function render(ChatRepositoryInterface $chatRepository)
    {
        $this->authorizing('show_chats');
        $groups = $chatRepository->getAllAdminListGroup($this->search);
        $this->data['status'] = $chatRepository->getStatus();
        return view('livewire.admin.chats.index-chat',['groups'=>$groups , 'open' => $chatRepository->openStatus(),'close' => $chatRepository->closeStatus()])
            ->extends('livewire.admin.layouts.admin');
    }


    public function openChatList($id , ChatRepositoryInterface $chatRepository)
    {
        $this->authorizing('show_chats');
        $this->chatList = $chatRepository->find($id);
        $this->chats = $this->chatList;
        $this->chatUserId = $this->chatList->user1 == auth()->id() ? $this->chatList->user2 : $this->chatList->user1;
    }

    public function blockChat(ChatRepositoryInterface $chatRepository)
    {
        $this->authorizing('edit_chats');
        if (!empty($this->chatList )){
            $this->chatList->status = $chatRepository->closeStatus();
            $chatRepository->save($this->chatList);
        }
    }

    public function unBlockChat(ChatRepositoryInterface $chatRepository)
    {
        $this->authorizing('edit_chats');
        if (!empty($this->chatList )){
            $this->chatList->status = $chatRepository->openStatus();
            $chatRepository->save($this->chatList);
        }
    }

    public function deleteChat(ChatRepositoryInterface $chatRepository)
    {
        $this->authorizing('delete_chats');
        if (!empty($this->chatList )){
            $chatRepository->delete($this->chatList);
            $this->reset(['chatList']);
        }
    }

}

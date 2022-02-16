<?php

namespace App\Http\Livewire\Site\Dashboard\Chats;
use App\Http\Livewire\BaseComponent;
use App\Models\Chat;
use App\Models\Notification;
use App\Models\Setting;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class IndexChat extends BaseComponent
{
    use TextBuilder;
    public $chatList = [] , $chatText;
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
            $chat->receiver_id = $this->chatList->user1 == \auth()->id() ? $this->chatList->user2 : $this->chatList->user1;
            $chat->content = $this->chatText;
            $chat->is_admin = auth()->user()->hasRole('admin');
            $chat->group_id = $this->chatList->id;
            $chat->save();
            $this->chatList = auth()->user()->contacts()->findOrFail($this->chatList->id);
            $receiver = $this->chatList->user1 == \auth()->id() ? $this->chatList->user_two : $this->chatList->user_one;
            $this->reset(['chatText']);
            $text = $this->createText('new_message',$receiver);
            $send = new SendMessages();
            $send->sends($text,$receiver,Notification::CHAT);
        }
    }

    public function render()
    {
        $groups = auth()->user()->contacts()->get();
        return view('livewire.site.dashboard.chats.index-chat',['groups'=>$groups]);
    }
}

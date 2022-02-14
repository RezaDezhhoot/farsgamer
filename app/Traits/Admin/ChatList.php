<?php
namespace App\Traits\Admin;

use App\Models\Chat;
use App\Models\ChatGroup;

trait ChatList
{
    public $chats , $chatText , $chatUserId;
    public function sendChatText()
    {
        if (!empty(preg_replace('/\s+/', '', $this->chatText)) && !is_null(trim($this->chatText))) {
            if (!empty($this->chatUserId)){
                $id = $this->chatUserId;
                $chat = new Chat();
                $group = \auth()->user()->singleContact($id);
                if (is_null($group)) {
                    $group = new ChatGroup();
                    $group->slug = uniqid();
                    $group->user1 = \auth()->id();
                    $group->user2 = $id;
                    $group->status = ChatGroup::OPEN;
                    $group->save();
                }
                $chat->sender_id = \auth()->id();
                $chat->receiver_id = $id;
                $chat->content = $this->chatText;
                $chat->is_admin = auth()->user()->hasRole('admin');
                $chat->group_id = $group->id;
                $chat->save();
                $this->chats = \auth()->user()->singleContact($id);
                $this->reset(['chatText']);
            }
        }
    }
}

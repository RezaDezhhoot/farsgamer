<?php
namespace App\Traits\Admin;

use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

trait ChatList
{
    public $chats , $chatText , $chatUserId;
    public function sendChatText(ChatRepositoryInterface $chatRepository , UserRepositoryInterface $userRepository)
    {
        if (!empty(preg_replace('/\s+/', '', $this->chatText)) && !is_null(trim($this->chatText))) {
            if (!empty($this->chatUserId)){
                $id = $this->chatUserId;
                $group = $chatRepository->singleContact($id);
                if (is_null($group) || empty($group))
                    $group = $chatRepository->startChat($id);

                $chat = [
                    'sender_id' => auth()->id(),
                    'receiver_id'=> $id,
                    'content' => $this->chatText,
                    'is_admin' => $userRepository->hasRole('admin'),
                    'group_id' => $group->id,
                ];
                $chat = $chatRepository->sendMessage($chat);
                $this->chats = $chatRepository->startChat($id);
                if (method_exists($this,'reset'))
                    $this->reset(['chatText']);

                return $chat;
            }
        }
    }
}

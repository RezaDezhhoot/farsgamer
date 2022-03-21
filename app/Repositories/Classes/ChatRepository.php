<?php


namespace App\Repositories\Classes;

use App\Models\ChatGroup;
use App\Repositories\Interfaces\ChatRepositoryInterface;


class ChatRepository implements ChatRepositoryInterface
{
    public function startChat($id)
    {
        $contact = $this->singleContact($id);
        if ($contact === null){
            $contact = ChatGroup::create([
                'slug' => uniqid(),
                'user1' => auth()->id(),
                'user2' => $id,
                'status' => ChatGroup::OPEN,
            ]);
        }
        return $contact;
    }

    public function contacts()
    {
        return ChatGroup::where(function ($query){
            return $query->where('user1',auth()->id())->orWhere('user2',auth()->id());
        });
    }

    public function singleContact($id)
    {
        return $this->contacts()->where(function ($query) use ($id) {
            if ($id == auth()->id())
                return $query->whereColumn('user1', 'user2');
            else
                return $query->where('user1',$id)->orWhere('user2',$id)->first();
        })->first();
    }

    public function sendMessage($chat)
    {

    }
}

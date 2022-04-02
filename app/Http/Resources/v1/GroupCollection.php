<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item){
            return [
                'id' => $item->id,
                'user1' => new User($item->user_one),
                'user2' => new User($item->user_two),
                'unread_messages_count' => $item->unread,
                'status_label' => $item->status_label,
                'last' => [
                    'text' => $item->last_text,
                    'date' => $item->last,
                    'sender_id' => $item->last_sender,
                ],
            ];
        });
    }
}

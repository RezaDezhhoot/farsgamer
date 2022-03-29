<?php

namespace App\Http\Resources\v1\Panel;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatCollection extends ResourceCollection
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
                'sender_id' => $item->sender_id,
                'receiver_id' => $item->receiver_id,
                'content' => $item->content,
                'is_admin' => $item->is_admin,
                'group_id' => $item->group_id,
                'is_read' => $item->is_read,
            ];
        });
    }
}

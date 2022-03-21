<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  $this->collection->map(function ($item){
            return [
                'subject' => $item->subject_label,
                'content' => $item->content,
                'type'  => $item->type,
                'date' => $item->created_at->diffForHumans(),
            ];
        });
    }
}

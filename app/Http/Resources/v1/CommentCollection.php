<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;


class CommentCollection extends ResourceCollection
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
                'content' => $item->content,
                'score' => $item->score,
                'created_at' => $item->created_at->diffForHumans(),
                'author'=>[
                    'id' => $item->user->id,
                    'user_name' => $item->user->user_name,
                    'profile_image' => asset($item->user->profile_image),
                ]
            ];
        });

    }
}

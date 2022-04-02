<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\v1\Panel\ChatCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed user_one
 * @property mixed user_two
 * @property mixed status_label
 * @property mixed chats
 */
class Group extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user1' => new User($this->user_one),
            'user2' => new User($this->user_two),
            'status_label' => $this->status_label,
            'chats' => new ChatCollection($this->chats),
        ];
    }
}

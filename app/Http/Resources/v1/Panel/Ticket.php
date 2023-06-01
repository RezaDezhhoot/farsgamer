<?php

namespace App\Http\Resources\v1\Panel;

use App\Http\Resources\v1\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed subject
 * @property mixed content
 * @property mixed file
 * @property mixed sender
 * @property mixed priority
 * @property mixed priority_label
 * @property mixed status
 * @property mixed status_label
 * @property mixed sender_type
 * @property mixed date
 * @property mixed child
 */
class Ticket extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array(
            'id' => $this->id,
            'subject' => $this->subject,
            'content' => $this->content,
            'files' => collect(explode(',',$this->file))->map(function ($item){
                return asset($item);
            }),
            'user_id' => $this->user_id,
            'sender' => new User($this->sender),
            'priority' => $this->priority,
            'priority_label' => $this->priority_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'sender_type' => $this->sender_type,
            'date' => $this->date,
            'children' => $this->child->map(function ($item){
                return new Ticket($item);
            })
        );
    }
}

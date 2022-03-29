<?php

namespace App\Http\Resources\v1\Panel;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TicketCollection extends ResourceCollection
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
                'subject' => $item->subject,
                'priority_label' => $item->priority_label,
                'status_label' => $item->status_label,
                'date' => $item->date,
            ];
        });
    }
}

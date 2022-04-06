<?php

namespace App\Http\Resources\v1\Panel;

use App\Http\Resources\v1\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item){
            return [
                'id' => $item->id,
                'code' => $item->code,
                'customer' => new User($item->customer),
                'seller' => new User($item->seller),
                'status_label' => $item->status_label,
                'referred' => $item->is_returned,
                'timer' => $item->timer->diffForHumans(),
                'date' => $item->date,
                'order' => [
                    'name' => $item->order->slug,
                    'image' => asset($item->order->image),
                ]
            ];
        });
    }
}

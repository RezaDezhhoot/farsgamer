<?php

namespace App\Http\Resources\v1\Panel;

use App\Http\Resources\v1\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class Bookmark extends JsonResource
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
            'order' => Order::make($this->order)
        ];
    }
}

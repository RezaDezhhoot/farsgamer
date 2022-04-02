<?php

namespace App\Http\Resources\v1\Panel;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CardCollection extends ResourceCollection
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
                'card_number' => $item->card_number,
                'card_sheba' => $item->card_sheba,
                'bank_label' => $item->bank_label,
                'status_label' => $item->status_label,
            ];
        });
    }
}

<?php

namespace App\Http\Resources\v1\Panel;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property mixed file
 * @property mixed price
 * @property mixed status_label
 * @property mixed link
 * @property mixed track_id
 * @property mixed date
 * @property mixed result
 * @property mixed card
 */
class RequestCollection extends ResourceCollection
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
                'price' => number_format($item->price),
                'status_label' => $item->status_label,
                'link' => $item->link,
                'files' => collect(explode(',',$item->file))->map(fn ($item2) => asset($item2)),
                'track_id' => $item->track_id,
                'date' => $item->date,
                'result' => $item->result,
                'card' => new Card($item->card),
            ];
        });
    }
}

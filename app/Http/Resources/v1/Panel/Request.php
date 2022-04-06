<?php

namespace App\Http\Resources\v1\Panel;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed price
 * @property mixed status_label
 * @property mixed link
 * @property mixed file
 * @property mixed track_id
 * @property mixed date
 * @property mixed result
 * @property mixed card
 * @property mixed id
 */
class Request extends JsonResource
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
            'price' => $this->price,
            'status_label' => $this->status_label,
            'link' => $this->link,
            'files' => collect(explode(',',$this->file))->map(fn ($item2) => asset($item2)),
            'track_id' => $this->track_id,
            'date' => $this->date,
            'result' => $this->result,
            'card' => new Card($this->card),
        ];
    }
}

<?php

namespace App\Http\Resources\v1\Panel;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed card_number
 * @property mixed card_sheba
 * @property mixed bank_label
 * @property mixed status_label
 * @property mixed status
 * @property mixed bank
 */
class Card extends JsonResource
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
            'card_number' => $this->card_number,
            'card_sheba' => $this->card_sheba,
            'bank_label' => $this->bank_label,
            'bank' => $this->bank,
            'status_label' => $this->status_label,
            'status' => $this->status,
        ];
    }
}

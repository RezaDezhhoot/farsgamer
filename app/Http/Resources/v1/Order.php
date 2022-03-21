<?php

namespace App\Http\Resources\v1;

use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed price
 * @property mixed slug
 * @property mixed image
 * @property mixed created_at
 * @property mixed platforms
 * @property mixed user
 * @property mixed view_count
 * @property mixed content
 * @property mixed gallery_asset
 * @property mixed parameters
 * @method parameters()
 */
class Order extends JsonResource
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
            'slug' => $this->slug,
            'price' => $this->price,
            'image' => asset($this->image),
            'gallery' => $this->gallery_asset,
            'view_count' => $this->view_count,
            'province' => isset($this->province) ? Setting::getProvince()[$this->province] : null,
            'city' => (isset($this->province) && isset($this->city)) ? Setting::getCity()[$this->province][$this->city] : null,
            'created_at' => $this->created_at->diffForHumans(),
            'content' => $this->content,
            'platforms' => new PlatformCollection($this->platforms),
            'parameters' => new ParameterCollection($this->parameters),
            'user' => [
                'user_name' => $this->user->user_name,
                'user_profile' => asset($this->user->profile_image),
                'phone' => $this->user->phone,
            ],
        ];
    }
}

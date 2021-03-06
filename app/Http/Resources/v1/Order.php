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
 * @property mixed commission
 * @property mixed intermediary
 * @property mixed category
 * @property mixed province_label
 * @property mixed city_label
 * @property mixed status_label
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
            'name' => $this->slug,
            'price' => $this->price,
            'price_unit' => 'toman',
            'image' => asset($this->image),
            'gallery' => collect(explode(',',$this->gallery_asset))->map(fn ($item) => asset($item)),
            'status_label' => $this->status_label,
            'view_count' => $this->view_count,
            'province' => isset($this->province) ? $this->province_label : null,
            'city' => (isset($this->province) && isset($this->city)) ? $this->city_label : null,
            'created_at' => $this->created_at->diffForHumans(),
            'content' => $this->content,
            'platforms' => new PlatformCollection($this->platforms),
            'parameters' => new ParameterCollection($this->parameters),
            'commission' => $this->commission,
            'intermediary' => $this->intermediary,
            'seoDescription' => $this->category->seo_description,
            'seoKeywords' => $this->category->seo_keywords,
            'user' => [
                'user_name' => $this->user->user_name,
                'user_profile' => asset($this->user->profile_image),
                'phone' => $this->user->phone,
            ],
        ];
    }
}

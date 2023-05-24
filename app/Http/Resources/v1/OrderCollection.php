<?php

namespace App\Http\Resources\v1;

use App\Models\Setting;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class OrderCollection extends ResourceCollection
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
                'name' => $item->slug,
                'price' => $item->price,
                'status_label' => $item->status_label,
                'image' => asset($item->image),
                'province' => isset($item->province) ? Setting::getProvince()[$item->province] : null,
                'city' => (isset($item->province) && isset($item->city)) ? Setting::getCity()[$item->province][$item->city] : null,
                'created_at' => $item->created_at->diffForHumans(),
                'category' => [
                    'id' => $item->category->id,
                    'slug' => $item->category->slug,
                    'title' => $item->category->title,
                    'default_image' => asset($item->category->default_image),
                ],
                'platforms' => new PlatformCollection($item->platforms),
                'user' => [
                    'id' => $item->user->id,
                    'user_name' => $item->user->user_name,
                    'user_profile' => $item->user->profile_image,
                ],
            ];
        });
    }
}

<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use function PHPUnit\Framework\isNull;

class CategoryCollection extends ResourceCollection
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
                'title' => $item->title,
                'logo' => asset($item->logo),
                'slider' => asset($item->slider),
                'orders_count' => $item->orders()->count(),
                'sub_categories' => !is_null($item->sub_categories) ? $item->sub_categories->map(function ($item){
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'logo' => asset($item->logo),
                        'slider' => asset($item->slider),
                        'orders_count' => $item->orders()->count(),
                    ];
                }) : [],
            ];
        });
    }
}

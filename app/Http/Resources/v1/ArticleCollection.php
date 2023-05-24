<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
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
                'slug' => $item->slug,
                'title' => $item->title,
                'main_image' => asset($item->main_image),
                'score' => $item->score,
                'status_label' => $item->status_label,
                'created_at' => $item->created_at->diffForHumans(),
                'updated_at' => $item->updated_at->diffForHumans(),
                'categories' => new ArticleCategoryCollection($item->categories),
                'author' => [
                    'name' => $item->author->name,
                    'user_name' => $item->author->user_name,
                    'profile_image' => $item->author->profile_image,
                ]
            ];
        });
    }
}

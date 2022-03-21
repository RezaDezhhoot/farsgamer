<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed slug
 * @property mixed title
 * @property mixed main_image
 * @property mixed content
 * @property mixed score
 * @property mixed status_label
 * @property mixed view_count
 * @property mixed commentable
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed categories
 * @property mixed author
 * @property mixed google_indexing
 */
class Article extends JsonResource
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
            'title' => $this->title,
            'main_image' => asset($this->main_image),
            'content' => $this->content,
            'score' => $this->score,
            'status_label' => $this->status_label,
            'view_count' => $this->view_count,
            'google_indexing' => $this->google_indexing,
            'commentable' => $this->commentable,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'categories' => new ArticleCategoryCollection($this->categories),
            'author' => [
                'name' => $this->author->name,
                'user_name' => $this->author->user_name,
                'profile_image' => asset($this->author->profile_image),
            ]
        ];
    }
}

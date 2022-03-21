<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed phone
 * @property mixed user_name
 * @property mixed name
 * @property mixed province_label
 * @property mixed city_label
 * @property mixed email
 * @property mixed status_label
 * @property mixed profile_image
 * @property mixed description
 * @property mixed score
 * @property mixed status
 */
class User extends JsonResource
{
    private $token;
    public function __construct($resource , $token = null)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'user_name' => $this->user_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'profile_image' => asset($this->profile_image),
            'description' => $this->description,
            'score' => $this->score,
            'province' => $this->province_label,
            'city' => $this->city_label,
            'token' => $this->token,
        ];
    }
}

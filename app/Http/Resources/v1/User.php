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
 * @property mixed baned
 * @property mixed ban
 * @property mixed balance
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
            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'wallet' => $this->balance,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'profile_image' => !empty($this->profile_image) ? asset($this->profile_image) : env('APP_URL').\App\Models\User::DEFAULT_IMAGE,
            'description' => $this->description,
            'score' => $this->score,
            'province' => $this->province_label,
            'city' => $this->city_label,
            'token' => $this->token,
            'baned' => $this->baned,
            'baned_timer' => $this->ban,
            'role' => $this->hasRole('admin') ? 'مدیر' : 'کاربر'
        ];
    }
}

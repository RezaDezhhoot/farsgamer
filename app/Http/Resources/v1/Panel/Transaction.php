<?php

namespace App\Http\Resources\v1\Panel;

use App\Http\Resources\v1\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed seller
 * @property mixed customer
 * @property mixed status
 * @property mixed status_label
 * @property mixed is_returned
 * @property mixed return_cause
 * @property mixed return_images
 * @property mixed timer
 * @property mixed intermediary
 * @property mixed commission
 * @property mixed received_status
 * @property mixed received_result
 * @property mixed order
 * @property mixed category
 * @property mixed data
 * @property mixed code
 * @property mixed id
 */
class Transaction extends JsonResource
{
    private $orderTransactionRepository , $current_status_data;
    public function __construct($resource , $data , $orderTransactionRepository)
    {
        parent::__construct($resource);
        $this->orderTransactionRepository = $orderTransactionRepository;
        $this->current_status_data = $data;
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
            'code' => $this->code,
            'seller' => new User($this->seller),
            'customer' => new User($this->customer),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'refunded' => $this->is_returned,
            'refunded_cause' => $this->return_cause,
            'refunded_images' => collect(explode(',',$this->return_images))->map(fn($item) => asset($item)),
            'commission' => $this->commission,
            'intermediary' => $this->intermediary,
            'timer' => $this->timer,
            'received_status' => $this->orderTransactionRepository::receiveStatus()[$this->received_status ?? 0],
            'received_result' => $this->received_result,
            'data' => [
                'id' => $this->data->id,
                'name' => $this->data->name,
                'value' => $this->data->value,
                'transfer' => !is_null($this->data->send) ? [
                    'id'=> $this->data->send->id,
                    'name'=> $this->data->send->slug,
                    'logo'=> asset($this->data->send->logo),
                    'send_time_inner_city'=> $this->data->send->send_time_inner_city,
                    'send_time_outer_city'=> $this->data->send->send_time_outer_city,
                    'description'=> $this->data->send->note,
                    'web_site'=> $this->data->send->pursuit_web_site,
                ] : [],
                'transfer_result' => $this->data->transfer_result,
            ],
            'current_status_data' => $this->current_status_data,
            'order' => [
                'name' => $this->order->slug,
                'need_an_interface' => $this->order->control,
                'image' => asset($this->order->image),
                'price' => $this->order->price,
                'province'=> $this->order->province_label,
                'city'=> $this->order->city_label,
            ],
        ];
    }
}

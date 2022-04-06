<?php

namespace App\Http\Resources\v1\Panel;

use App\Http\Resources\v1\User;
use Illuminate\Contracts\Support\Arrayable;
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
    public function __construct($resource , $data = [], $orderTransactionRepository = null)
    {
        parent::__construct($resource);
        $this->orderTransactionRepository = $orderTransactionRepository;
        $this->current_status_data = $data;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'data' => [
                'id' => $this->data->id,
                'name' => $this->data->name,
                'value' => collect(json_decode($this->data->value,true))->map(function ($item,$key){
                    $form_name = collect(json_decode($this->category->forms,true))->where('name',$key)->first();
                    return [
                        'label' => $form_name['label'],
                        'value' => $item
                    ];
                }),
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
            'current_status_data' => $this->current_status_data,
            'seller' => new User($this->seller),
            'customer' => new User($this->customer),
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

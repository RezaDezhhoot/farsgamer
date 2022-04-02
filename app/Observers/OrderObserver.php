<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Report;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class OrderObserver
{
    use TextBuilder;
    public $send;

    public function __construct()
    {
        $this->send = new SendMessages();
    }
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        $text = [];
        $text = $this->createText('new_order',$order);
        $this->send->sends($text,$order->user,Notification::ADDRESS,$order->id);
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        $text = [];
        switch ($order->status){
            case Order::IS_CONFIRMED : $text = $this->createText('confirm_order',$order);break;
            case Order::IS_REJECTED : $text = $this->createText('reject_order',$order);break;
            case Order::IS_REQUESTED : $text = $this->createText('request_order',$order);break;
        }
        $this->send->sends($text,$order->user,Notification::ORDER,$order->id);
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        $text = [];
        $text = $this->createText('delete_order',$order);
        $this->send->sends($text,$order->user,Notification::ORDER,$order->id);
        Report::create([
            'subject' => Notification::ORDER,
            'action' => Report::DELETED,
            'row_status' => $order->status_label,
            'user_id' => $order->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        $this->deleted($order);
    }
}

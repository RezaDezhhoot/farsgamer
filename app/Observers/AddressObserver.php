<?php

namespace App\Observers;

use App\Models\Address;
use App\Models\Notification;
use App\Models\Report;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class AddressObserver
{
    use TextBuilder;
    public $send;
    public function __construct()
    {
        $this->send = new SendMessages();
    }

    /**
     * Handle the Address "created" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function created(Address $address)
    {
        Report::create([
            'subject' => Notification::ADDRESS,
            'action' => Report::CREATED,
            'row_status' => $address->status_label,
            'user_id' => $address->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Address "updated" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function updated(Address $address)
    {
        $text = [];
        switch ($address->status){
            case Address::CONFIRMED:{
                $text = $this->createText('confirm_address',$address);
                break;
            }
            case Address::NOT_CONFIRMED:{
                $text = $this->createText('reject_address',$address);
                break;
            }
        }
        $this->send->sends($text,$address->user,Notification::ADDRESS,$address->id);
        Report::create([
            'subject' => Notification::ADDRESS,
            'action' => Report::UPDATED,
            'row_status' => $address->status_label,
            'user_id' => $address->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Address "deleted" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function deleted(Address $address)
    {
        $text = [];
        $text = $this->createText('delete_address',$address);
        $this->send->sends($text,$address->user,Notification::ADDRESS,$address->id);
        Report::create([
            'subject' => Notification::ADDRESS,
            'action' => Report::DELETED,
            'row_status' => $address->status_label,
            'user_id' => $address->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Address "restored" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function restored(Address $address)
    {

    }

    /**
     * Handle the Address "force deleted" event.
     *
     * @param  \App\Models\Address  $address
     * @return void
     */
    public function forceDeleted(Address $address)
    {
        $this->deleted($address);
    }
}

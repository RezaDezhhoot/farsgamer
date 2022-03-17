<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\Report;
use App\Models\Request;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class RequestObserver
{
    use TextBuilder;
    public $send;
    public function __construct()
    {
        $this->send = new SendMessages();
    }
    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Request $request)
    {
        Report::create([
            'subject' => Notification::REQUEST,
            'action' => Report::CREATED,
            'row_status' => $request->status_label,
            'user_id' => $request->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
        $text = [];
        $text = $this->createText('settlement_request',$request);
        $this->send->sends($text,$request->user,Notification::REQUEST,$request->id);
    }

    /**
     * Handle the Request "updated" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function updated(Request $request)
    {
        $text = [];
        switch ($request->status){
            case Request::SETTLEMENT:{
                $text = $this->createText('complete_request',$request);
                break;
            }
            case Request::REJECTED:{
                $text = $this->createText('rejected_request',$request);
                break;
            }
        }
        $this->send->sends($text,$request->user,Notification::REQUEST,$request->id);
    }

    /**
     * Handle the Request "deleted" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function deleted(Request $request)
    {
        //
    }

    /**
     * Handle the Request "restored" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function restored(Request $request)
    {
        //
    }

    /**
     * Handle the Request "force deleted" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function forceDeleted(Request $request)
    {
        //
    }
}

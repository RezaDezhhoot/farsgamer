<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\OrderTransaction;
use App\Models\Report;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class OrderTransactionObserver
{
    use TextBuilder;
    public $send;
    public function __construct()
    {
        $this->send = new SendMessages();
    }
    /**
     * Handle the OrderTransaction "created" event.
     *
     * @param  \App\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function created(OrderTransaction $orderTransaction)
    {
        $texts = $this->createText('confirm_transaction',$orderTransaction);
        $this->send->sends($texts,$orderTransaction->seller,Notification::TRANSACTION,$orderTransaction->id);
        Report::create([
            'subject' => Notification::TRANSACTION,
            'action' => Report::CREATED,
            'row_status' => OrderTransaction::getStatus()[OrderTransaction::WAIT_FOR_CONFIRM]['label'],
            'user_id' => $orderTransaction->seller->id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the OrderTransaction "updated" event.
     *
     * @param  \App\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function updated(OrderTransaction $orderTransaction)
    {
        //
    }

    /**
     * Handle the OrderTransaction "deleted" event.
     *
     * @param  \App\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function deleted(OrderTransaction $orderTransaction)
    {
        //
    }

    /**
     * Handle the OrderTransaction "restored" event.
     *
     * @param  \App\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function restored(OrderTransaction $orderTransaction)
    {
        //
    }

    /**
     * Handle the OrderTransaction "force deleted" event.
     *
     * @param  \App\Models\OrderTransaction  $orderTransaction
     * @return void
     */
    public function forceDeleted(OrderTransaction $orderTransaction)
    {
        //
    }
}

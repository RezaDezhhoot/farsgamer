<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\OrderTransaction;
use App\Models\Payment;
use App\Models\Report;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class PaymentObserver
{
    use TextBuilder;
    /**
     * Handle the Payment "created" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function created(Payment $payment)
    {
        Report::create([
            'subject' => Notification::PAYMENT,
            'action' => Report::CREATED,
            'row_status' => $payment->status_label,
            'user_id' => $payment->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Payment "updated" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function updated(Payment $payment)
    {
        Report::create([
            'subject' => Notification::PAYMENT,
            'action' => Report::UPDATED,
            'row_status' => $payment->status_label,
            'user_id' => $payment->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
        if ($payment->status_code == 100){
            $texts = $this->createText('pay',$payment);
            $send = new SendMessages();
            $send->sends($texts,$payment->user,Notification::TRANSACTION,$payment->id);
        }
    }

    /**
     * Handle the Payment "deleted" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function deleted(Payment $payment)
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function restored(Payment $payment)
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function forceDeleted(Payment $payment)
    {
        //
    }
}

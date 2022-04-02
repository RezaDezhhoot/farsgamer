<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\Report;
use App\Models\Ticket;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class TicketObserver
{
    use TextBuilder;
    public $send;
    public function __construct()
    {
        $this->send = new SendMessages();
    }

    /**
     * Handle the Ticket "created" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        Report::create([
            'subject' => Notification::TICKET,
            'action' => Report::CREATED,
            'row_status' => $ticket->status_label,
            'user_id' => $ticket->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
        $text = [];
        if ($ticket->status == Ticket::PENDING)
            $text = $this->createText('new_ticket',$ticket);
        elseif ($ticket->status == Ticket::ANSWERED)
            $text = $this->createText('ticket_answer',$ticket);

        $this->send->sends($text,$ticket->user,Notification::TICKET,$ticket->id);
    }

    /**
     * Handle the Ticket "updated" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function updated(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the Ticket "deleted" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function restored(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function forceDeleted(Ticket $ticket)
    {
        //
    }
}

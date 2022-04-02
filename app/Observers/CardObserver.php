<?php

namespace App\Observers;

use App\Models\Card;
use App\Models\Notification;
use App\Models\Report;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class CardObserver
{
    use TextBuilder;
    public $send;
    /**
     * Handle the Card "created" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */

    public function __construct()
    {
        $this->send = new SendMessages();
    }

    public function created(Card $card)
    {
        Report::create([
            'subject' => Notification::CARD,
            'action' => Report::CREATED,
            'row_status' => $card->status_label,
            'user_id' => $card->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Card "updated" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function updated(Card $card)
    {
        $text = [];
        switch ($card->status){
            case Card::CONFIRMED:{
                $text = $this->createText('confirm_card',$card);
                break;
            }
            case Card::NOT_CONFIRMED:{
                $text = $this->createText('reject_card',$card);
                break;
            }
        }
        $this->send->sends($text,$card->user,Notification::CARD,$card->id);
        Report::create([
            'subject' => Notification::CARD,
            'action' => Report::UPDATED,
            'row_status' => $card->status_label,
            'user_id' => $card->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Card "deleted" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function deleted(Card $card)
    {
        $text = [];
        $text = $this->createText('delete_card',$card);
        $this->send->sends($text,$card->user,Notification::ADDRESS,$card->id);
        Report::create([
            'subject' => Notification::CARD,
            'action' => Report::DELETED,
            'row_status' => $card->status_label,
            'user_id' => $card->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Card "restored" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function restored(Card $card)
    {
        //
    }

    /**
     * Handle the Card "force deleted" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function forceDeleted(Card $card)
    {
        $this->deleted($card);
    }
}

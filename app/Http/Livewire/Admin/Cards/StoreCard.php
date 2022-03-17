<?php

namespace App\Http\Livewire\Admin\Cards;

use App\Http\Livewire\BaseComponent;
use App\Models\Notification;
use App\Traits\Admin\ChatList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Card;

class StoreCard extends BaseComponent
{
    use AuthorizesRequests  , ChatList;
    public $card , $header , $mode , $data = [];
    public $card_number , $card_sheba , $bank , $bank_logo , $status , $newMessageStatus , $message , $newMessage;

    public function mount($action , $id = null)
    {
        $this->authorize('show_cards');
        if ($action == 'edit')
        {
            $this->card = Card::findOrFail($id);
            $this->header = 'کارت بانکی کاربر '.$this->card->user->fullName;
            $this->card_number = $this->card->card_number;
            $this->card_sheba = $this->card->card_sheba;
            $this->bank = $this->card->bank;
            $this->bank_logo = $this->card->bank_logo;
            $this->status = $this->card->status;
        } else abort(404);

        $this->message = $this->card->user->alerts()->where([
            ['subject',Notification::CARD],
            ['model_id',$this->card->id],
        ])->get();
        $this->mode = $action;
        $this->data['status'] = Card::getStatus();
        $this->data['subject'] = Notification::getSubject();
        $this->data['bank'] = Card::bank();
        $this->chatUserId = $this->card->user->id;
        $this->chats = \auth()->user()->singleContact($this->card->user->id);
        $this->newMessageStatus = Notification::CARD;
    }
    public function render()
    {
        return view('livewire.admin.cards.store-card')->extends('livewire.admin.layouts.admin');
    }

    public function deleteItem()
    {
        $this->authorize('delete_cards');
        $this->card->delete();
        return redirect()->route('admin.card');
    }

    public function store()
    {
        $this->authorize('edit_cards');
        $this->saveInDataBase($this->card);
    }

    public function saveInDataBase(Card $model)
    {
        $this->validate([
            'card_number' => ['required','size:16','regex:/(^([0-9]+)$)/u','unique:cards,card_number,'.($this->card->id ?? 0)],
            'card_sheba' => ['required','size:26','regex:/(^(IR)([0-9]+)$)/u','unique:cards,card_sheba,'.($this->card->id ?? 0)],
            'bank' => ['required','string'],
            'bank_logo' => ['required','string'],
            'status' => ['required','in:'.Card::CONFIRMED.','.Card::NOT_CONFIRMED],
        ], [],[
            'card_number' => 'شماره کارت',
            'card_sheba' => 'شماره شبا',
            'bank' => 'بانک',
            'bank_logo' => 'لوگو بانک',
        ]);
        $model->card_number = $this->card_number;
        $model->card_sheba = $this->card_sheba;
        $model->bank = $this->bank;
        $model->bank_logo = $this->bank_logo;
        $model->status = $this->status;
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => ['required','string','max:255'],
            'newMessageStatus' => ['required','in:'.implode(',',array_keys(Notification::getSubject()))]
        ],[],[
            'newMessage'=> 'متن',
            'newMessageStatus' => 'وضعیت پیام'
        ]);
        $result = new Notification();
        $result->subject = Notification::CARD;
        $result->content = $this->newMessage;
        $result->type = Notification::PRIVATE;
        $result->user_id = $this->card->user->id;
        $result->model = Notification::CARD;
        $result->model_id = $this->card->id;
        $result->save();
        $this->message->push($result);
        $this->reset(['newMessage','newMessageStatus']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

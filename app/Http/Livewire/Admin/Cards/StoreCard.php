<?php

namespace App\Http\Livewire\Admin\Cards;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\Admin\ChatList;

class StoreCard extends BaseComponent
{
    use ChatList;
    public $card , $header , $mode , $data = [];
    public $card_number , $card_sheba , $bank  , $status , $newMessageStatus , $message , $newMessage;

    public function mount(
        CardRepositoryInterface $cardRepository , ChatRepositoryInterface $chatRepository ,
        NotificationRepositoryInterface $notificationRepository, UserRepositoryInterface $userRepository , $action , $id = null
    )
    {
        $this->authorizing('show_cards');
        if ($action == 'edit')
        {
            $this->card = $cardRepository->find($id , false);
            $this->header = 'کارت بانکی کاربر '.$this->card->user->fullName;
            $this->card_number = $this->card->card_number;
            $this->card_sheba = $this->card->card_sheba;
            $this->bank = $this->card->bank;
            $this->status = $this->card->status;
        } else abort(404);

        $this->message = $userRepository->getUserNotifications($this->card->user,$notificationRepository->cardStatus(),$this->card->id);

        $this->mode = $action;
        $this->data['status'] = $cardRepository->getStatus();
        $this->data['subject'] = $notificationRepository->getSubjects();
        $this->data['bank'] = $cardRepository->getBank();
        $this->chatUserId = $this->card->user->id;
        $this->chats =$chatRepository->singleContact($this->card->user->id);
        $this->newMessageStatus = $notificationRepository->cardStatus();
    }
    public function render()
    {
        return view('livewire.admin.cards.store-card')
            ->extends('livewire.admin.layouts.admin');
    }

    public function deleteItem(CardRepositoryInterface $cardRepository)
    {
        $this->authorizing('delete_cards');
        $cardRepository->delete($this->card);
        return redirect()->route('admin.card');
    }

    public function store(CardRepositoryInterface $cardRepository)
    {
        $this->authorizing('edit_cards');
        $this->saveInDataBase($cardRepository , $this->card);
    }

    public function saveInDataBase($cardRepository, $model)
    {
        $this->validate([
            'card_number' => ['required','size:16','regex:/(^([0-9]+)$)/u','unique:cards,card_number,'.($this->card->id ?? 0)],
            'card_sheba' => ['required','size:26','regex:/(^(IR)([0-9]+)$)/u','unique:cards,card_sheba,'.($this->card->id ?? 0)],
            'bank' => ['required','string'],
            'status' => ['required','in:'.implode(',',array_keys($cardRepository->getStatus()))],
        ], [],[
            'card_number' => 'شماره کارت',
            'card_sheba' => 'شماره شبا',
            'bank' => 'بانک',
        ]);
        $model->card_number = $this->card_number;
        $model->card_sheba = $this->card_sheba;
        $model->bank = $this->bank;
        $model->status = $this->status;
        $cardRepository->save($model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function sendMessage(NotificationRepositoryInterface $notificationRepository)
    {
        $this->validate([
            'newMessage' => ['required','string','max:255'],
            'newMessageStatus' => ['required','in:'.implode(',',array_keys($notificationRepository->getSubjects()))]
        ],[],[
            'newMessage'=> 'متن',
            'newMessageStatus' => 'وضعیت پیام'
        ]);
        $notification = [
            'subject' => $notificationRepository->cardStatus(),
            'content' =>  $this->newMessage,
            'type' => $notificationRepository->privateType(),
            'user_id' => $this->card->user->id,
            'model' => $notificationRepository->cardStatus(),
            'model_id' => $this->card->id
        ];
        $notification = $notificationRepository->create($notification);
        $this->message->push($notification);
        $this->reset(['newMessage','newMessageStatus']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

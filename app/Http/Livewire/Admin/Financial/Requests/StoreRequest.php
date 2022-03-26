<?php

namespace App\Http\Livewire\Admin\Financial\Requests;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\Admin\ChatList;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;

class StoreRequest extends BaseComponent
{
    use ChatList;
    public $request , $header , $mode , $data = [];
    public $status , $result , $file , $link , $track_id , $user , $phone , $sheba , $bank , $card , $price;
    public $newMessage , $message , $newMessageStatus;


    public function mount(
        RequestRepositoryInterface $requestRepository,NotificationRepositoryInterface $notificationRepository,
        CardRepositoryInterface $cardRepository,UserRepositoryInterface $userRepository ,
        ChatRepositoryInterface $chatRepository,$action , $id =null
    )
    {
        $this->authorizing('show_requests');
        if ($action == 'edit')
        {
            $this->request = $requestRepository->find($id);
            $this->header = 'درخواست شماره '.$id;
            $this->status = $this->request->status;
            $this->price = number_format($this->request->price);
            $this->result = $this->request->result;
            $this->file = $this->request->file;
            $this->link = $this->request->link;
            $this->track_id = $this->request->track_id;
            $this->user = $this->request->user->fullName;
            $this->phone = $this->request->user->phone;
            $this->card = $this->request->card->card_number;
            $this->sheba = $this->request->card->card_sheba;
            $this->bank = $cardRepository->getBank()[$this->request->card->bank];
            $this->chatUserId = $this->request->user->id;
            $this->chats = $chatRepository  ->singleContact($this->request->user->id);
        } else abort(404);

        $this->message = $userRepository->getUserNotifications($this->request->user,$notificationRepository->requestStatus(),$this->request->id);

        $this->data['status'] = $requestRepository::getStatus();
        $this->data['subject'] = $notificationRepository->getSubjects();
        $this->newMessageStatus = $notificationRepository->requestStatus();
    }

    public function store(RequestRepositoryInterface $requestRepository)
    {
        $this->authorizing('edit_requests');
        $this->saveInDateBase($requestRepository,$this->request);
    }

    public function saveInDateBase($requestRepository, $model)
    {
        $this->validate([
            'status' => ['required','in:'.implode(',',array_keys($requestRepository->getStatus()))],
            'result' => ['nullable','string','max:5600'],
            'file' => ['nullable','string','max:250'],
            'link' => ['nullable','url','max:250'],
            'track_id' => ['nullable','numeric','max:250'],
        ],[],[
            'status' => 'وضعیت',
            'result' => 'نتیجه',
            'file' => 'تصویر رسید',
            'link' => 'لینک پیگیری',
            'track_id' => 'کد پیگیری',
        ]);

        if ($this->status == $requestRepository::rejectedStatus() && $model->status == $requestRepository::newStatus()) {
            $model->user->deposit($model->price, ['description' =>  $model->result , 'from_admin'=> true]);
        } elseif ($this->status == $requestRepository::settlementStatus() && $model->status == $requestRepository::rejectedStatus()) {
            try {
                $this->user->forceWithdraw($this->price, ['description' => $this->result, 'from_admin'=> true]);
            } catch (BalanceIsEmpty | InsufficientFunds $exception) {
                return $this->addError('walletAmount', $exception->getMessage());
            }
        } elseif ($this->status == $requestRepository::rejectedStatus() && $model->status == $requestRepository::settlementStatus()) {
            $model->user->deposit($model->price, ['description' =>  $model->result , 'from_admin'=> true]);
        }

        $model->status = $this->status;
        $model->result = $this->result;
        $model->file = $this->file;
        $model->link = $this->link;
        $model->track_id = $this->track_id;
        $requestRepository->save($model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }


    public function render()
    {
        return view('livewire.admin.financial.requests.store-request')
            ->extends('livewire.admin.layouts.admin');
    }
    public function sendMessage(NotificationRepositoryInterface $notificationRepository)
    {
        $this->validate([
            'newMessage' => ['required','string'],
            'newMessageStatus' => ['required','in:'.implode(',',array_keys($notificationRepository->getSubjects()))]
        ],[],[
            'newMessage'=> 'متن',
            'newMessageStatus' => 'وضعیت پیام'
        ]);
        $notification = [
            'subject' => $notificationRepository->requestStatus(),
            'content' =>  $this->newMessage,
            'type' => $notificationRepository->privateType(),
            'user_id' => $this->request->user->id,
            'model' => $notificationRepository->requestStatus(),
            'model_id' => $this->request->id
        ];
        $notification = $notificationRepository->create($notification);
        $this->message->push($notification);
        $this->reset(['newMessage','newMessageStatus']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

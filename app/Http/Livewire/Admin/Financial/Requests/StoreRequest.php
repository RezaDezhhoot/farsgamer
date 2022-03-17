<?php

namespace App\Http\Livewire\Admin\Financial\Requests;

use App\Http\Livewire\BaseComponent;
use App\Models\Card;
use App\Models\Notification;
use App\Traits\Admin\ChatList;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Request;

class StoreRequest extends BaseComponent
{
    use AuthorizesRequests  , ChatList;
    public $request , $header , $mode , $data = [];
    public $status , $result , $file , $link , $track_id , $user , $phone , $sheba , $bank , $card , $price;
    /**
     * @var mixed
     */
    public $newMessage;
    /**
     * @var mixed
     */
    public $message;
    /**
     * @var mixed|string
     */
    public $newMessageStatus;

    public function mount($action , $id =null)
    {
        $this->authorize('show_requests');
        if ($action == 'edit')
        {
            $this->request = Request::findOrFail($id);
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
            $this->bank = Card::bank()[$this->request->card->bank];
            $this->chatUserId = $this->request->user->id;
            $this->chats = \auth()->user()->singleContact($this->request->user->id);
        } else abort(404);

        $this->message = $this->request->user->alerts()->where([
            ['subject',Notification::REQUEST],
            ['model_id',$this->request->id],
        ])->get();
        $this->data['status'] = Request::getStatus();
        $this->data['subject'] = Notification::getSubject();
        $this->newMessageStatus = Notification::REQUEST;
    }

    public function store()
    {
        $this->authorize('edit_requests');
        $this->saveInDateBase($this->request);
    }

    public function saveInDateBase(Request $model)
    {
        $this->validate([
            'status' => ['required','in:'.Request::SETTLEMENT.','.Request::REJECTED],
            'result' => ['nullable','string'],
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

        if ( $this->status == Request::REJECTED && $model->status == Request::NEW) {
            $model->user->deposit($model->price, ['description' =>  $model->result , 'from_admin'=> true]);
        } elseif ($this->status == Request::SETTLEMENT && $model->status == Request::REJECTED) {
            try {
                $this->user->forceWithdraw($this->price, ['description' => $this->result, 'from_admin'=> true]);
            } catch (BalanceIsEmpty | InsufficientFunds $exception) {
                return $this->addError('walletAmount', $exception->getMessage());
            }
        } elseif ($this->status == Request::REJECTED && $model->status == Request::SETTLEMENT) {
            $model->user->deposit($model->price, ['description' =>  $model->result , 'from_admin'=> true]);
        }

        $model->status = $this->status;
        $model->result = $this->result;
        $model->file = $this->file;
        $model->link = $this->link;
        $model->track_id = $this->track_id;
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }


    public function render()
    {
        return view('livewire.admin.financial.requests.store-request')->extends('livewire.admin.layouts.admin');
    }
    public function sendMessage()
    {
        $this->authorize('edit_orders');
        $this->validate([
            'newMessage' => ['required','string'],
            'newMessageStatus' => ['required','in:'.implode(',',array_keys(Notification::getSubject()))]
        ],[],[
            'newMessage'=> 'متن',
            'newMessageStatus' => 'وضعیت پیام'
        ]);
        $result = new Notification();
        $result->subject = Notification::REQUEST;
        $result->content = $this->newMessage;
        $result->type = Notification::PRIVATE;
        $result->user_id = $this->request->user->id;
        $result->model = Notification::REQUEST;
        $result->model_id = $this->request->id;
        $result->save();
        $this->message->push($result);
        $this->reset(['newMessage','newMessageStatus']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

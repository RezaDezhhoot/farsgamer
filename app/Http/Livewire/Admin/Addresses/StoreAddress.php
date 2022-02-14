<?php

namespace App\Http\Livewire\Admin\Addresses;

use App\Models\Notification;
use App\Models\Setting;
use App\Traits\Admin\ChatList;
use App\Traits\Admin\Sends;
use App\Traits\Admin\TextBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Livewire\BaseComponent;
use App\Models\Address;
use App\Sends\SendMessages;

class StoreAddress extends BaseComponent
{
    use AuthorizesRequests , Sends , TextBuilder , ChatList;
    public $address , $header , $mode , $data = [];
    public $country , $province , $city , $addressText , $postal_code ,$first_name , $last_name , $phone , $email , $status;
    /**
     * @var mixed
     */
    public $message;
    /**
     * @var mixed
     */
    public $newMessage;
    /**
     * @var mixed|string
     */
    public $newMessageStatus;

    public function mount($action , $id = null)
    {
        $this->authorize('show_addresses');
        if ($action == 'edit')
        {
            $this->address = Address::findOrFail($id);
            $this->header = $this->address->user->fullName;
            $this->country = $this->address->country;
            $this->province = $this->address->province;
            $this->city = $this->address->city;
            $this->addressText = $this->address->address;
            $this->postal_code = $this->address->postal_code;
            $this->first_name = $this->address->first_name;
            $this->last_name = $this->address->last_name;
            $this->phone = $this->address->phone;
            $this->email = $this->address->email;
            $this->status = $this->address->status;
        } else abort(404);

        $this->data['status'] = Address::getStatus();
        $this->mode = $action;
        $this->chatUserId = $this->address->user->id;
        $this->chats = \auth()->user()->singleContact($this->address->user->id);
        $this->message = $this->address->user->alerts()->where([
            ['subject',Notification::ADDRESS],
            ['model_id',$this->address->id],
        ])->get();
        $this->data['subject'] = Notification::getSubject();
        $this->newMessageStatus = Notification::ADDRESS;
    }

    public function store()
    {
        $this->authorize('edit_addresses');
        $this->saveInDateBase($this->address);
    }

    public function saveInDateBase(Address $model)
    {
        $fields = [
            'status' => ['required','in:'.Address::CONFIRMED.','.Address::NOT_CONFIRMED],
        ];
        $messages = [
            'status' => 'وضعیت',
        ];
        $this->validate($fields,[],$messages);
        if ($this->status <> $model->status)
            $this->notify();

        $model->status = $this->status;
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function render()
    {
        $this->data['province'] = Setting::getProvince();
        $this->data['city'] = Setting::getCity()[$this->province];
        return view('livewire.admin.addresses.store-address')->extends('livewire.admin.layouts.admin');
    }

    public function notify()
    {
        $text = [];
        switch ($this->status){
            case Address::CONFIRMED:{
                $text = $this->createText('confirm_address',$this->address);
                break;
            }
            case Address::NOT_CONFIRMED:{
                $text = $this->createText('reject_address',$this->address);
                break;
            }
        }
        $send = new SendMessages();
        $send->sends($text,$this->address->user,Notification::ADDRESS,$this->address->id);
    }

    public function deleteItem()
    {
        $this->authorize('delete_addresses');
        $this->address->delete();
        return redirect()->route('admin.address');
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
        $result->subject = Notification::ADDRESS;
        $result->content = $this->newMessage;
        $result->type = Notification::PRIVATE;
        $result->user_id = $this->address->user->id;
        $result->model = Notification::ADDRESS;
        $result->model_id = $this->address->id;
        $result->save();
        $text = $this->createText('new_message',$this->address->user);
        $send = new SendMessages();
        $send->sends($text,$this->address->user,Notification::REQUEST);
        $this->message->push($result);
        $this->reset(['newMessage','newMessageStatus']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

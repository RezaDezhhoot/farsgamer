<?php

namespace App\Http\Livewire\Site\Dashboard\Accounting;

use App\Http\Livewire\BaseComponent;
use App\Models\Card;
use App\Models\Notification;
use App\Traits\Admin\Sends;
use App\Traits\Admin\TextBuilder;
use App\Models\Request;
use App\Sends\SendMessages;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;

class StoreAccounting extends BaseComponent
{
    use   TextBuilder;
    public $user;
    public $request , $price , $cart , $status , $result , $file , $link , $track_id  , $mode , $header , $data = [];
    public function mount($action , $id)
    {
        if ($action == 'show') {
            $this->user = auth()->user();
            $this->request = $this->user->requests()->findOrFail($id);
            $this->price = $this->request->price;
            $this->cart = $this->request->cart;
            $this->status = $this->request->status;
            $this->result = $this->request->result;
            $this->file = $this->request->file;
            $this->link = $this->request->link;
            $this->track_id = $this->request->track_id;
            $this->header = 'درخواست شماره '.$this->request->id;
        } elseif ($action == 'edit') {
            $this->header = 'درخواست جدید';
        } else abort(404);
        $this->mode = $action;
        $this->data['status'] = Request::getStatus();
        $this->data['carts'] = $this->user->carts()->where('status',Card::CONFIRMED)->pluck('card_number','id');
    }

    public function store()
    {
        if ($this->mode == 'create' && $this->user->ballance >= $this->price) if (in_array($this->cart,$this->data['carts']->toArray())) {
            $this->validate([
                'price' => ['required','numeric','between:0,9999999999999.9999999'],
                'card_id' => ['required','exists:carts,id'],
            ]);
            try {
                $this->user->forceWithdraw($this->price, ['description' => ' تسویه حساب', 'from_admin'=> true]);
            } catch (BalanceIsEmpty | InsufficientFunds $exception) {
                return $this->addError('price', $exception->getMessage());
            }

            $request = new Request();
            $request->price = $this->price;
            $request->user_id  = $this->user->id;
            $request->card_id = $this->cart;
            $request->status = Request::NEW;
            $request->save();

            $text = $this->createText('settlement_request',$request);
            $send = new SendMessages();
            $send->sends($text,$this->user,Notification::REQUEST,$request->id);
            $this->reset(['price','cart']);
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        } else {
            $this->addError('price','مبلغ درخواستی کمتر از حد مجاز');
        }
    }

    public function render()
    {
        return view('livewire.site.dashboard.accountings.store-accounting');
    }
}

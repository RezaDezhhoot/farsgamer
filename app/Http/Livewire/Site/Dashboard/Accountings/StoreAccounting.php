<?php

namespace App\Http\Livewire\Site\Dashboard\Accountings;

use App\Traits\Admin\Sends;
use App\Traits\Admin\TextBuilder;
use Livewire\Component;
use App\Models\Request;

class StoreAccounting extends Component
{
    use  Sends , TextBuilder;
    public $request , $price , $cart , $status , $result , $file , $link , $track_id , $backed , $mode , $header , $data = [];
    public function mount($action , $id)
    {
        if ($action == 'show') {
            $this->request = auth()->user()->requests()->findOrFail($id);
            $this->price = $this->request->price;
            $this->cart = $this->request->cart;
            $this->status = $this->request->status;
            $this->result = $this->request->result;
            $this->file = $this->request->file;
            $this->link = $this->request->link;
            $this->track_id = $this->request->track_id;
            $this->backed = $this->request->backed;
            $this->header = 'درخواست شماره '.$this->request->id;
        } elseif ($action == 'edit') {
            $this->header = 'درخواست جدید';
        } else abort(404);
        $this->mode = $action;
        $this->data['status'] = Request::getStatus();
        $this->data['carts'] = auth()->user()->carts()->pluck('card_number','id');
    }

    public function store()
    {
        if ($this->mode == 'create' && auth()->user()->wallet->due_amount >= $this->price) {
            if (in_array($this->cart,auth()->user()->carts()->pluck('id')->toArray())) {
                $this->validate([
                    'price' => ['required','numeric','between:0,9999999999999.9999999'],
                    'card_id' => ['required','exists:carts,id'],
                    'status' => Request::NEW,
                ]);
                $request = new Request();
                $request->price = $this->price;
                $request->user_id  = auth()->id();
                $request->card_id = $this->card_id;
                $request->status = $this->status;
                $request->save();
                $text = $this->createText('settlement_request',$request);
                $this->sends($text,$request->user);
                auth()->user()->wallet->charge($request->price,'deduction');
                $this->reset(['price','card_id','status']);
                $this->emitNotify('اطلاعات با موفقیت ثبت شد');
            }
        } else {
            $this->addError('price','مبلغ درخواستی کمتر از حد مجاز');
            return;
        }
    }

    public function render()
    {
        return view('livewire.site.dashboard.accountings.store-accounting');
    }
}

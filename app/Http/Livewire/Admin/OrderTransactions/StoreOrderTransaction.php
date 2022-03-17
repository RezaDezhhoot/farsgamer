<?php

namespace App\Http\Livewire\Admin\OrderTransactions;

use App\Http\Livewire\BaseComponent;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderTransactionPayment;
use App\Models\OrderTransactionData;
use App\Sends\SendMessages;
use App\Traits\Admin\ChatList;
use App\Traits\Admin\TextBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\OrderTransaction;
use Illuminate\Support\Carbon;

class StoreOrderTransaction extends BaseComponent
{
    use AuthorizesRequests , TextBuilder  , ChatList;
    public  $transaction , $mode , $header;
    public  $status,$nowStatus , $send_id , $transfer_result , $return_cause , $return_images , $newMessageStatus;
    public  $data = [] , $return , $chat ;
    public $newMessage , $message , $newMessageSubject , $timer , $form = []  , $transactionData = [] , $allChats;
    public function mount($action,$id = null)
    {
        $this->authorize('show_transactions');
        if ($action == 'edit')
        {
            $this->transaction = OrderTransaction::findOrFail($id);
            $this->header = $this->transaction->order->slug;
            $this->form = json_decode($this->transaction->order->category->forms,true) ?? [];
            $this->message = Notification::where([
                ['subject',Notification::TRANSACTION],
                ['model_id',$this->transaction->id],
            ])->where(function ($query){
                return $query->where('user_id',$this->transaction->seller->id)->orWhere('user_id',$this->transaction->customer->id);
            })->get();

            OrderTransactionData::updateOrCreate(['order_transaction_id'=>$this->transaction->id],['name'=>uniqid(),'updated_at'=>Carbon::now()]);

            $this->allChats = $this->transaction->getChatBetweenCustomerAndSeller();
            $this->transactionData = json_decode($this->transaction->data['value'],true);
            $this->send_id = $this->transaction->data->send_id ?? '';
            $this->transfer_result = $this->transaction->data->transfer_result ?? '';
            $this->return_cause = $this->transaction->return_cause;
            $this->return_images = $this->transaction->return_images;
            $this->return = $this->transaction->is_returned;
            $this->status = $this->transaction->status;
            $this->nowStatus = $this->transaction->status;

            $this->chatUserId = $this->transaction->seller->id;
            $this->chats = \auth()->user()->singleContact($this->transaction->seller->id);

        } else abort(404);

        $this->data['received_status'] = OrderTransaction::receiveStatus();

        $this->data['for'] = [
            'seller' => 'فروشنده',
            'customer' => 'خریدار',
        ];
        $this->data['messageSubject'] = Notification::getSubject();
        $this->data['send'] =[
            $this->transaction->seller->id => 'فروشنده',
            $this->transaction->customer->id => 'خریدار',
            '0' => 'هر دو نفر',
        ];
        $this->mode = $action;
        $this->data['transfer'] = $this->transaction->order->category->sends->pluck('slug','id');
        $this->newMessageSubject = Notification::TRANSACTION;
    }

    public function setChat($target)
    {
        if ($target == 'customer'){
            $this->chatUserId = $this->transaction->customer->id;
            $this->chats = \auth()->user()->singleContact($this->transaction->customer->id);
        } elseif ($target == 'seller'){
            $this->chatUserId = $this->transaction->seller->id;
            $this->chats = \auth()->user()->singleContact($this->transaction->seller->id);
        }
    }

    public function render()
    {
        foreach ($this->transaction::getStatus($this->return) as $key => $item) {
            $this->data['status'][$key] = $item['label'];
        }

        return view('livewire.admin.order-transactions.store-order-transaction' , ['transaction' => $this->transaction])
            ->extends('livewire.admin.layouts.admin');
    }

    public function setTimer()
    {
        $this->emit('timer',['data' => $this->transaction->timer->toDateTimeString()]);
    }

    public function store()
    {
        $this->authorize('edit_transactions');
        // cancel
        if ($this->status == OrderTransaction::IS_CANCELED && !in_array($this->transaction->status,[OrderTransaction::WAIT_FOR_COMPLETE,
                OrderTransaction::WAIT_FOR_TESTING,OrderTransaction::IS_RETURNED,OrderTransaction::IS_CANCELED,OrderTransaction::WAIT_FOR_RECEIVE
            ])) {
            if ($this->transaction->is_returned){
                $this->emitNotify('برای این معامله امکان تغییر وجود ندارد','warning');
                return (false);
            }
            $this->transaction->order->status = Order::IS_CONFIRMED;
            OrderTransactionData::where('transaction_id',$this->transaction->id)->update([
                'value'=> null
            ]);
            if (!empty($this->transaction->payment) && $this->transaction->payment->status == OrderTransactionPayment::SUCCESS){
                $price = $this->transaction->payment->price;
                $this->transaction->customer->deposit($price,
                    ['description' => $this->transaction->code.'بازگشت هزینه بابت معامله با کد ' , 'from_admin'=> true]);
            }
            $this->transaction->order->save();
            $this->transaction->status = $this->status;
            $this->nowStatus = $this->status;
            $this->transaction->save();
            return $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        } elseif ($this->status == OrderTransaction::IS_CANCELED) {
            $this->emitNotify('برای این معامله امکان تغییر وجود ندارد','warning');
            return (false);
        }
        // end cancel

        $carbon = Carbon::make(now());
        $timer = $carbon;
        $data = $this->transaction->data;
        $this->resetErrorBag();
        if (!in_array($this->transaction->status,[OrderTransaction::IS_CANCELED,OrderTransaction::WAIT_FOR_COMPLETE])){
            if ($this->transaction->status <> $this->status) {
                $this->transaction->order->OrderTransactions()->where('id','!=',$this->transaction->id)->update([
                    'status' => OrderTransaction::IS_CANCELED,
                ]);
                // data
                if (!is_null($this->transactionData))
                    $data->value = json_encode($this->transactionData) ?? '';

                if ($this->transaction->order->category->type == Category::PHYSICAL) {
                    $this->validate([
                        'send_id' => ['required','exists:sends,id'],
                        'transfer_result' => ['nullable','string','max:250']
                    ],[],[
                        'send_id' => 'روش ارسال',
                        'transfer_result' => 'کد رهگیری'
                    ]);
                    $data->send_id = $this->send_id;
                    $data->transfer_result = $this->transfer_result;
                }
                $data->save();
                // end data

                if ($this->transaction->is_returned == 0) {
                    $this->validate([
                        'status' => ['required','in:'.OrderTransaction::WAIT_FOR_RECEIVE.','.OrderTransaction::WAIT_FOR_PAY.','
                            .OrderTransaction::WAIT_FOR_CONFIRM. ','.OrderTransaction::WAIT_FOR_SEND.','. OrderTransaction::WAIT_FOR_TESTING.','
                            .OrderTransaction::WAIT_FOR_CONTROL.','.OrderTransaction::WAIT_FOR_COMPLETE.','
                            .OrderTransaction::IS_RETURNED.','.OrderTransaction::WAIT_FOR_NO_RECEIVE
                        ],
                    ],[],[
                        'status' => 'وضعیت',
                    ]);
                    if ($this->status == OrderTransaction::WAIT_FOR_CONFIRM)
                        $this->transaction->order->status = Order::IS_CONFIRMED;
                    elseif ($this->transaction->status == OrderTransaction::IS_RETURNED){
                        $send = new SendMessages();
                        $text = $this->createText('reject_returned_transaction',$this->transaction);
                        $send->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                        $send->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                    }
                    elseif ($this->status == OrderTransaction::WAIT_FOR_RECEIVE && $this->transaction->order->category->type == Category::DIGITAL)
                        $timer = (float)$this->transaction->order->category->receive_time;
                    elseif ($this->status == OrderTransaction::WAIT_FOR_RECEIVE && $this->transaction->order->category->type == Category::PHYSICAL) {
                        if ($this->transaction->order->city == $this->transaction->customer->city && $this->transaction->order->province == $this->transaction->customer->province)
                            $timer = (float)$this->transaction->data->send->send_time_inner_city;
                        else
                            $timer = (float)$this->transaction->data->send->send_time_outer_city;
                    }
                    elseif ($this->status == OrderTransaction::WAIT_FOR_COMPLETE){
                        $this->transaction->order->status = Order::IS_FINISHED;
                        if (!empty($this->transaction->payment) && $this->transaction->payment->status == OrderTransactionPayment::SUCCESS){
                            $commission = $this->transaction->commission;
                            $intermediary = $this->transaction->intermediary;
                            $price = $this->transaction->payment->price - $commission - $intermediary;
                            $this->transaction->seller->deposit($price,
                                ['description' => $this->transaction->code.'واربز هزینه بابت معامله به کد ', 'from_admin'=> true]);
                        }
                    }
                } elseif ($this->transaction->is_returned == 1){
                    $this->validate([
                        'status' => ['required','in:'.OrderTransaction::WAIT_FOR_SENDING_DATA.','.OrderTransaction::WAIT_FOR_SEND .','
                            .OrderTransaction::WAIT_FOR_CONTROL.','.OrderTransaction::IS_CANCELED.','
                            .OrderTransaction::WAIT_FOR_RECEIVE.','.OrderTransaction::WAIT_FOR_NO_RECEIVE
                        ],
                    ],[],[
                        'status' => 'وضعیت',
                    ]);
                    if ($this->status == OrderTransaction::WAIT_FOR_RECEIVE && $this->transaction->order->category->type == Category::DIGITAL)
                        $timer = (float)$this->transaction->order->category->receive_time;
                    elseif ($this->status == OrderTransaction::WAIT_FOR_RECEIVE && $this->transaction->order->category->type == Category::PHYSICAL) {
                        if ($this->transaction->order->city == $this->transaction->seller->city && $this->transaction->order->province == $this->transaction->seller->province)
                            $timer = (float)$this->transaction->data->send->send_time_inner_city;
                        else
                            $timer = (float)$this->transaction->data->send->send_time_outer_city;
                    }
                }
                if ($this->status == OrderTransaction::WAIT_FOR_TESTING)
                    $timer = (float)$this->transaction->order->category->guarantee_time;
                elseif ($this->status == OrderTransaction::WAIT_FOR_PAY)
                    $timer = (float)$this->transaction->order->category->pay_time;
                elseif ($this->status == OrderTransaction::WAIT_FOR_SEND)
                    $timer = (float)$this->transaction->order->category->send_time;
                elseif ($this->status == OrderTransaction::WAIT_FOR_SENDING_DATA)
                    $timer = (float)$this->transaction->order->category->sending_data_time;
                elseif ($this->status == OrderTransaction::WAIT_FOR_NO_RECEIVE)
                    $timer = (float)$this->transaction->order->category->no_receive_time;

                $timer = $carbon->addMinutes($timer);

                $this->transaction->timer = $timer;
                $this->emit('timer',['data' => $timer->toDateTimeString()]);
                $this->transaction->status = $this->status;
                $this->nowStatus = $this->status;
                $this->transaction->save();

                $this->transaction->order->save();
                $this->notify();
            }
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        } else{
            $this->emitNotify('برای این معامله امکان تغییر وجود ندارد','warning');
            return (false);
        }
    }

    public function sendNewMessage()
    {
        $this->authorize('edit_transactions');
        $this->validate([
            'newMessage' => ['required','string'],
            'newMessageSubject' => ['required','in:'.implode(',',array_keys(Notification::getSubject()))],
            'newMessageStatus' => ['required','in:0,'.$this->transaction->seller->id.','.$this->transaction->customer->id],
        ],[],[
            'newMessage'=> 'متن',
            'newMessageSubject' => 'موضوع پیام',
            'newMessageStatus' => 'ارسال برای'
        ]);
        $send = new SendMessages();
        if ($this->newMessageStatus == 0) {
            $codes = ['seller','customer'];
            foreach ($codes as $code) {
                $result = new Notification();
                $result->subject = Notification::TRANSACTION;
                $result->content = $this->newMessage;
                $result->type = Notification::PRIVATE;
                $result->model = Notification::TRANSACTION;
                $result->model_id = $this->transaction->id;
                $result->user_id = $this->transaction->{$code}->id;
                $result->save();
                $this->message->push($result);
            }
        } else {
            $result = new Notification();
            $result->subject = Notification::TRANSACTION;
            $result->content = $this->newMessage;
            $result->type = Notification::PRIVATE;
            $result->model = Notification::TRANSACTION;
            $result->model_id = $this->transaction->id;
            if ($this->newMessageStatus == $this->transaction->seller->id)
                $result->user_id = $this->transaction->seller->id;
            elseif ($this->newMessageStatus == $this->transaction->customer->id)
                $result->user_id = $this->transaction->customer->id;

            $result->save();
            $this->message->push($result);
        }
        $this->reset(['newMessage','newMessageStatus','newMessageSubject']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function sendToReturn()
    {
        $this->authorize('edit_transactions');
        if ($this->transaction->status == OrderTransaction::IS_RETURNED) {
            $this->transaction->is_returned = true;
            $this->transaction->status = OrderTransaction::WAIT_FOR_SENDING_DATA;
            $this->status = OrderTransaction::WAIT_FOR_SENDING_DATA;
            $data = $this->transaction->data;
            $data->send_id  = null;
            $data->transfer_result = null;
            $data->save();

            $this->transaction->save();
            $this->return = true;
            $this->data['status'] = OrderTransaction::returnedStatus();
            $timer = Carbon::make(now())->addMinutes((float)OrderTransaction::getTimer(OrderTransaction::WAIT_FOR_SENDING_DATA));
            $this->transaction->timer = $timer;
            $this->emit('timer',['data' => $timer->toDateTimeString()]);
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
            $send = new SendMessages();
            $text = $this->createText('confirm_returned_transaction',$this->transaction);
            $send->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
            $send->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
            $this->notify();
            return;
        }
        else $this->addError('error','برای انتقال ابتدا وضعیت را به مرجوع شده تغییر دهید');
    }

    public function notify()
    {
        $text = [];
        $send = new SendMessages();
        switch ($this->status){
            case OrderTransaction::WAIT_FOR_CONFIRM:{
                $text = $this->createText('confirm_transaction',$this->transaction);
                $model = $this->transaction->seller;
                $send->sends($text,$model,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::WAIT_FOR_PAY:{
                $text = $this->createText('pay_transaction',$this->transaction);
                $model = $this->transaction->customer;
                $send->sends($text,$model,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::WAIT_FOR_SEND:{
                if ($this->return == 1) {
                    $text = $this->createText('returned_send_transaction',$this->transaction);
                    $model = $this->transaction->customer;
                } else {
                    $text = $this->createText('send_transaction',$this->transaction);
                    $model = $this->transaction->seller;
                }
                $send->sends($text,$model,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::WAIT_FOR_RECEIVE:{
                if ($this->return == 1) {
                    $text = $this->createText('returned_receive_transaction',$this->transaction);
                    $model = $this->transaction->seller;
                } else {
                    $text = $this->createText('receive_transaction',$this->transaction);
                    $model = $this->transaction->customer;
                }
                $send->sends($text,$model,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::WAIT_FOR_NO_RECEIVE:{
                if ($this->return == 1) {
                    $text = $this->createText('return_no_receive_transaction',$this->transaction);
                    $model = $this->transaction->customer;
                } else {
                    $text = $this->createText('no_receive_transaction',$this->transaction);
                    $model = $this->transaction->seller;
                }
                $send->sends($text,$model,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::WAIT_FOR_TESTING:{
                $text = $this->createText('test_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                $send->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::WAIT_FOR_COMPLETE:{
                $text = $this->createText('complete_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                $send->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::IS_RETURNED:{
                $text = $this->createText('request_to_return_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::WAIT_FOR_SENDING_DATA:{
                $text = $this->createText('send_data_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
            case OrderTransaction::IS_CANCELED:{
                $text = $this->createText('cancel_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                $send->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                break;
            }
        }
    }
}

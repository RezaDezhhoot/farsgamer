<?php

namespace App\Http\Livewire\Admin\OrderTransactions;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Sends\SendMessages;
use App\Traits\Admin\ChatList;
use App\Traits\Admin\TextBuilder;
use Illuminate\Support\Carbon;

class StoreOrderTransaction extends BaseComponent
{
    use  TextBuilder  , ChatList;
    public  $transaction , $mode , $header;
    public  $status,$nowStatus , $send_id , $transfer_result , $return_cause , $return_images , $newMessageStatus;
    public  $data = [] , $return , $chat ;
    public $newMessage , $message , $newMessageSubject , $timer , $form = []  , $transactionData = [] , $allChats;
    public function mount(
        OrderTransactionRepositoryInterface $orderTransactionRepository,NotificationRepositoryInterface $notificationRepository,
        UserRepositoryInterface $userRepository,ChatRepositoryInterface $chatRepository,
        $action,$id = null
    )
    {
        $this->authorizing('show_transactions');
        if ($action == 'edit')
        {
            $this->transaction = $orderTransactionRepository->find($id);
            $this->header = $this->transaction->order->slug;
            $this->form = json_decode($this->transaction->order->category->forms,true) ?? [];
            $this->message = $userRepository->getUsersNotifications([$this->transaction->seller,$this->transaction->customer],$notificationRepository->transactionStatus(),
            $this->transaction->id);
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
            $this->chats = $chatRepository->singleContact($this->transaction->seller->id);

        } else abort(404);

        $this->data['received_status'] = $orderTransactionRepository::receiveStatus();

        $this->data['for'] = $orderTransactionRepository::getFor();

        $this->data['messageSubject'] = $notificationRepository->getSubjects();
        $this->data['send'] =[
            $this->transaction->seller->id => 'فروشنده',
            $this->transaction->customer->id => 'خریدار',
            '0' => 'هر دو نفر',
        ];
        $this->mode = $action;
        $this->data['transfer'] = $this->transaction->order->category->sends->pluck('slug','id');
        $this->newMessageSubject = $notificationRepository->transactionStatus();
    }

    public function setChat(ChatRepositoryInterface $chatRepository,$target)
    {
        if ($target == 'customer'){
            $this->chatUserId = $this->transaction->customer->id;
            $this->chats = $chatRepository->singleContact($this->transaction->customer->id);
        } elseif ($target == 'seller'){
            $this->chatUserId = $this->transaction->seller->id;
            $this->chats = $chatRepository->singleContact($this->transaction->seller->id);
        }
    }

    public function render(OrderTransactionRepositoryInterface $orderTransactionRepository , CategoryRepositoryInterface $categoryRepository)
    {
        foreach ($orderTransactionRepository::getStatus($this->return) as $key => $item)
            $this->data['status'][$key] = $item['label'];

        $standardStatus = $orderTransactionRepository::standardStatus();
        $returnedStatus = $orderTransactionRepository::returnedStatus();
        $returnStatus = $orderTransactionRepository::isReturned();
        $noReceiveStatus = $orderTransactionRepository::noReceive();
        $physical = $categoryRepository::physical();
        $data = [
            'transaction' =>  $this->transaction,
            'standardStatus' => $standardStatus,
            'returnedStatus' => $returnedStatus,
            'returnStatus' => $returnStatus,
            'noReceiveStatus' => $noReceiveStatus,
            'physical' => $physical,
            'sendingData' => $orderTransactionRepository::sendingData(),
            'send' => $orderTransactionRepository::send(),
            'pay' => $orderTransactionRepository::pay()
        ];
        return view('livewire.admin.order-transactions.store-order-transaction' , $data)->extends('livewire.admin.layouts.admin');
    }

    public function setTimer()
    {
        $this->emit('timer',['data' => $this->transaction->timer->toDateTimeString()]);
    }

    public function store(
        OrderTransactionRepositoryInterface $orderTransactionRepository , NotificationRepositoryInterface $notificationRepository ,
        CategoryRepositoryInterface $categoryRepository , OrderRepositoryInterface $orderRepository
    )
    {
        $this->authorizing('edit_transactions');
        $subject = $notificationRepository->transactionStatus();
        // cancel
        if ($this->status == $orderTransactionRepository::cancel() && !in_array($this->transaction->status,[$orderTransactionRepository::complete(),
                $orderTransactionRepository::test(),$orderTransactionRepository::isReturned(),$orderTransactionRepository::cancel(),
                $orderTransactionRepository::receive()
            ])) {
            if ($this->transaction->is_returned){
                $this->emitNotify('برای این معامله امکان تغییر وجود ندارد','warning');
                return (false);
            }
            $this->transaction->order->status = $orderRepository::isConfirmedStatus();
            $orderTransactionRepository->updateData($this->transaction,['value'=> null]);

            if (!empty($this->transaction->payment) && $this->transaction->payment->status == $orderTransactionRepository::hasPayment()){
                $price = $this->transaction->payment->price;
                $this->transaction->customer->deposit($price,
                    ['description' => $this->transaction->code.'بازگشت هزینه بابت معامله با کد ' , 'from_admin'=> true]);
            }
            $this->transaction->order->save();
            $this->transaction->status = $this->status;
            $this->nowStatus = $this->status;
            $this->transaction->save();
            return $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        } elseif ($this->status == $orderTransactionRepository::cancel()) {
            $this->emitNotify('برای این معامله امکان تغییر وجود ندارد','warning');
            return (false);
        }
        // end cancel

        $carbon = Carbon::make(now());
        $timer = $carbon;
        $data = $this->transaction->data;
        $this->resetErrorBag();
        if (!in_array($this->transaction->status,[$orderTransactionRepository::cancel(),$orderTransactionRepository::complete()])){
            if ($this->transaction->status <> $this->status) {
                $this->transaction->order->OrderTransactions()->where('id','!=',$this->transaction->id)->update([
                    'status' => $orderTransactionRepository::cancel(),
                ]);
                // data
                if (!is_null($this->transactionData))
                    $data->value = json_encode($this->transactionData) ?? '';

                if ($this->transaction->order->category->type == $categoryRepository::physical()) {
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
                        'status' => ['required','in:'.$orderTransactionRepository::receive().','.$orderTransactionRepository::pay().','
                            .$orderTransactionRepository::confirm().','.$orderTransactionRepository::send().','.$orderTransactionRepository::test().','
                            .$orderTransactionRepository::control().','.$orderTransactionRepository::complete().','
                            .$orderTransactionRepository::isReturned().','.$orderTransactionRepository::noReceive()
                        ],
                    ],[],[
                        'status' => 'وضعیت',
                    ]);

                    if ($this->transaction->status == $orderTransactionRepository::isReturned()){
                        $send = new SendMessages();
                        $text = $this->createText('reject_returned_transaction',$this->transaction);
                        $send->sends($text,$this->transaction->seller,$subject,$this->transaction->id);
                        $send->sends($text,$this->transaction->customer,$subject,$this->transaction->id);
                    }
                    if ($this->status == $orderTransactionRepository::confirm())
                        $this->transaction->order->status = $orderRepository::isConfirmedStatus();
                    else $this->transaction->order->status = $orderRepository::isRequestedStatus();

                    if ($this->status == $orderTransactionRepository::receive() && $this->transaction->order->category->type == $categoryRepository::digital())
                        $timer = (float)$this->transaction->order->category->receive_time;
                    elseif ($this->status == $orderTransactionRepository::receive() && $this->transaction->order->category->type == $categoryRepository::physical()) {
                        if ($this->transaction->order->city == $this->transaction->customer->city && $this->transaction->order->province == $this->transaction->customer->province)
                            $timer = (float)$this->transaction->data->send->send_time_inner_city;
                        else
                            $timer = (float)$this->transaction->data->send->send_time_outer_city;
                    }
                    elseif ($this->status == $orderTransactionRepository::complete()){
                        $this->transaction->order->status = $orderRepository::isFinishedStatus();
                        if (!empty($this->transaction->payment) && $this->transaction->payment->status == $orderTransactionRepository::hasPayment()){
                            $commission = $this->transaction->commission;
                            $intermediary = $this->transaction->intermediary;
                            $price = $this->transaction->payment->price - $commission - $intermediary;
                            $this->transaction->seller->deposit($price,
                                ['description' => $this->transaction->code.'واربز هزینه بابت معامله به کد ', 'from_admin'=> true]);
                        }
                    }
                } elseif ($this->transaction->is_returned == 1){
                    $this->validate([
                        'status' => ['required','in:'.$orderTransactionRepository::sendingData().','.$orderTransactionRepository::send().','
                            .$orderTransactionRepository::control().','.$orderTransactionRepository::cancel().','
                            .$orderTransactionRepository::receive().','.$orderTransactionRepository::noReceive().','.$orderTransactionRepository::complete()
                        ],
                    ],[],[
                        'status' => 'وضعیت',
                    ]);
                    if ($this->status == $orderTransactionRepository::receive() && $this->transaction->order->category->type == $categoryRepository::digital())
                        $timer = (float)$this->transaction->order->category->receive_time;
                    elseif ($this->status == $orderTransactionRepository::receive() && $this->transaction->order->category->type == $categoryRepository::physical()) {
                        if ($this->transaction->order->city == $this->transaction->seller->city && $this->transaction->order->province == $this->transaction->seller->province)
                            $timer = (float)$this->transaction->data->send->send_time_inner_city;
                        else
                            $timer = (float)$this->transaction->data->send->send_time_outer_city;
                    }
                }
                if ($this->status == $orderTransactionRepository::test())
                    $timer = (float)$this->transaction->order->category->guarantee_time;
                elseif ($this->status == $orderTransactionRepository::pay())
                    $timer = (float)$this->transaction->order->category->pay_time;
                elseif ($this->status == $orderTransactionRepository::send())
                    $timer = (float)$this->transaction->order->category->send_time;
                elseif ($this->status == $orderTransactionRepository::sendingData())
                    $timer = (float)$this->transaction->order->category->sending_data_time;
                elseif ($this->status == $orderTransactionRepository::noReceive())
                    $timer = (float)$this->transaction->order->category->no_receive_time;

                $timer = $carbon->addMinutes($timer);

                $this->transaction->timer = $timer;
                $this->emit('timer',['data' => $timer->toDateTimeString()]);
                $this->transaction->status = $this->status;
                $this->nowStatus = $this->status;
                $orderTransactionRepository->save($this->transaction);
                $orderRepository->save($this->transaction->order);
                $this->notify($notificationRepository , $orderTransactionRepository);
            }
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        } else{
            $this->emitNotify('برای این معامله امکان تغییر وجود ندارد','warning');
            return (false);
        }
    }

    public function sendNewMessage(NotificationRepositoryInterface $notificationRepository)
    {
        $this->authorizing('edit_transactions');
        $this->validate([
            'newMessage' => ['required','string'],
            'newMessageSubject' => ['required','in:'.implode(',',array_keys($notificationRepository->getSubjects()))],
            'newMessageStatus' => ['required','in:0,'.$this->transaction->seller->id.','.$this->transaction->customer->id],
        ],[],[
            'newMessage'=> 'متن',
            'newMessageSubject' => 'موضوع پیام',
            'newMessageStatus' => 'ارسال برای'
        ]);
        if ($this->newMessageStatus == 0) {
            $codes = ['seller','customer'];
            foreach ($codes as $code) {
                $notification = [
                    'subject' => $notificationRepository->transactionStatus(),
                    'content' => $this->newMessage,
                    'type' => $notificationRepository->privateType(),
                    'model' => $this->transaction->id,
                    'user_id' => $this->transaction->{$code}->id
                ];
                $notification = $notificationRepository->create($notification);
                $this->message->push($notification);
            }
        } else {
            $notification = [
                'subject' => $notificationRepository->transactionStatus(),
                'content' => $this->newMessage,
                'type' => $notificationRepository->privateType(),
                'model' => $this->transaction->id,
            ];
            if ($this->newMessageStatus == $this->transaction->seller->id)
                $notification['user_id'] = $this->transaction->seller->id;
            elseif ($this->newMessageStatus == $this->transaction->customer->id)
                $notification['user_id'] = $this->transaction->customer->id;

            $notification = $notificationRepository->create($notification);
            $this->message->push($notification);
        }
        $this->reset(['newMessage','newMessageStatus','newMessageSubject']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function sendToReturn(OrderTransactionRepositoryInterface $orderTransactionRepository
        , NotificationRepositoryInterface $notificationRepository)
    {
        $this->authorizing('edit_transactions');
        if ($this->transaction->status == $orderTransactionRepository::isReturned()) {
            $this->transaction->is_returned = true;
            $this->transaction->status = $orderTransactionRepository::sendingData();
            $this->status = $orderTransactionRepository::sendingData();
            $data = $this->transaction->data;
            $data->send_id  = null;
            $data->transfer_result = null;
            $orderTransactionRepository->saveData($data);

            $this->return = true;
            $this->data['status'] = $orderTransactionRepository::returnedStatus();
            $timer = Carbon::make(now())->addMinutes($this->transaction->order->category->sending_data_time);
            $this->transaction->timer = $timer;
            $orderTransactionRepository->save($this->transaction);
            $this->emit('timer',['data' => $timer->toDateTimeString()]);
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');

            $send = new SendMessages();
            $text = $this->createText('confirm_returned_transaction',$this->transaction);
            $send->sends($text,$this->transaction->seller,$notificationRepository->transactionStatus(),$this->transaction->id);
            $send->sends($text,$this->transaction->customer,$notificationRepository->transactionStatus(),$this->transaction->id);
            $this->notify($notificationRepository , $orderTransactionRepository);
            return;
        }
        else $this->addError('error','برای انتقال ابتدا وضعیت را به مرجوع شده تغییر دهید');
    }

    public function notify($notificationRepository , $orderTransactionRepository)
    {
        $text = [];
        $send = new SendMessages();
        $subject = $notificationRepository->transactionStatus();
        switch ($this->status){
            case $orderTransactionRepository::confirm():{
                $text = $this->createText('confirm_transaction',$this->transaction);
                $model = $this->transaction->seller;
                $send->sends($text,$model,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::pay():{
                $text = $this->createText('pay_transaction',$this->transaction);
                $model = $this->transaction->customer;
                $send->sends($text,$model,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::send():{
                if ($this->return == 1) {
                    $text = $this->createText('returned_send_transaction',$this->transaction);
                    $model = $this->transaction->customer;
                } else {
                    $text = $this->createText('send_transaction',$this->transaction);
                    $model = $this->transaction->seller;
                }
                $send->sends($text,$model,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::receive():{
                if ($this->return == 1) {
                    $text = $this->createText('returned_receive_transaction',$this->transaction);
                    $model = $this->transaction->seller;
                } else {
                    $text = $this->createText('receive_transaction',$this->transaction);
                    $model = $this->transaction->customer;
                }
                $send->sends($text,$model,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::noReceive():{
                if ($this->return == 1) {
                    $text = $this->createText('return_no_receive_transaction',$this->transaction);
                    $model = $this->transaction->customer;
                } else {
                    $text = $this->createText('no_receive_transaction',$this->transaction);
                    $model = $this->transaction->seller;
                }
                $send->sends($text,$model,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::test():{
                $text = $this->createText('test_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,$subject,$this->transaction->id);
                $send->sends($text,$this->transaction->customer,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::complete():{
                $text = $this->createText('complete_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,$subject,$this->transaction->id);
                $send->sends($text,$this->transaction->customer,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::isReturned():{
                $text = $this->createText('request_to_return_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::sendingData():{
                $text = $this->createText('send_data_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,$subject,$this->transaction->id);
                break;
            }
            case $orderTransactionRepository::cancel():{
                $text = $this->createText('cancel_transaction',$this->transaction);
                $send->sends($text,$this->transaction->seller,$subject,$this->transaction->id);
                $send->sends($text,$this->transaction->customer,$subject,$this->transaction->id);
                break;
            }
        }
    }
}

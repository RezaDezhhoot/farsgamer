<?php

namespace App\Http\Livewire\Site\Dashboard\Transactions;

use App\Http\Livewire\BaseComponent;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderTransactionData;
use App\Models\OrderTransactionPayment;
use App\Models\Payment as Pay;
use App\Models\OrderTransaction;
use App\Models\Setting;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class StoreTransaction extends BaseComponent
{
    use TextBuilder;
    public $transaction , $status ,$timer , $form = []  , $transactionData = [] , $transactionDataTransfer = [];
    public  $data = [] , $return , $chat , $message  , $received_result , $price , $gateway , $send_id , $transfer_result;
    public $return_cause , $return_images , $nowStatus , $mode;

    public function mount($action , $id){
        if ($action == 'show') {
            $this->transaction = OrderTransaction::where('seller_id',auth()->id())->orWhere('customer_id',auth()->id())
                ->findOrFail($id);

            $this->form = json_decode($this->transaction->order->category->forms,true) ?? [];
            $forms = [];
            foreach ($this->form as $form){
                if ($this->transaction->is_returned){
                    if ($form['status'] == 'return') {
                        if (auth()->id() == $this->transaction->seller_id){
                            if ($form['for'] == 'seller')
                                $forms[] = $form;
                        } elseif (auth()->id() == $this->transaction->customer_id) {
                            if ($form['for'] == 'customer')
                                $forms[] = $form;
                        }
                    }
                } else {
                    if ($form['status'] == 'normal') {
                        if (auth()->id() == $this->transaction->seller_id){
                            if ($form['for'] == 'seller')
                                $forms[] = $form;
                        } elseif (auth()->id() == $this->transaction->customer_id) {
                            if ($form['for'] == 'customer')
                                $forms[] = $form;
                        }
                    }
                }
            }
            $this->form = $forms;
            $this->message = Notification::where([
                ['subject',Notification::TRANSACTION],
                ['model_id',$this->transaction->id],
            ])->where('user_id',auth()->id)->get();

            $this->transactionData = json_decode($this->transaction->data['value'],true);
            $this->send_id = $this->transaction->data->send_id ?? '';
            $this->transfer_result = $this->transaction->data->transfer_result ?? '';
            $this->return_cause = $this->transaction->return_cause;
            $this->return_images = $this->transaction->return_images;
            $this->return = $this->transaction->is_returned;
            $this->status = $this->transaction->status;
            $this->nowStatus = $this->transaction->status;
        } else abort(404);

        $this->data['received_status'] = OrderTransaction::receiveStatus();
        $this->data['messageSubject'] = Notification::getSubject();
        $this->mode = $action;
        $this->data['transfer'] = $this->transaction->order->category->sends->pluck('slug','id');
    }

    public function render()
    {
        foreach ($this->transaction::getStatus($this->return) as $key => $item) {
            $this->data['status'][$key] = $item['label'];
        }

        return view('livewire.site.dashboard.order-transactions.store-transaction');
    }

    public function setTimer()
    {
        $this->emit('timer',['data' => $this->transaction->timer->toDateTimeString()]);
    }

    public function store()
    {
        if (in_array($this->transaction->status,[OrderTransaction::WAIT_FOR_COMPLETE,OrderTransaction::IS_CANCELED,OrderTransaction::IS_REQUESTED]))
            return(false);

        $timerStatus = Carbon::make(now())->diff($this->transaction->timer)->format('%r%s');
        $data = $this->transaction->data;
        $sms = new SendMessages();
        if (!$this->transaction->is_returned){
            switch ($this->transaction->status)
            {
                case OrderTransaction::WAIT_FOR_CONFIRM:{
                    if ((auth()->id() == $this->transaction->seller_id)) {
                        $this->transaction->status = OrderTransaction::WAIT_FOR_PAY;
                        $timer = Carbon::make(now())->addMinutes(
                            (float)$this->transaction->order->category->pay_time
                        );
                        $this->transaction->timer = $timer;
                        $sms->sends(
                            $this->createText('pay_transaction',$this->transaction),
                            $this->transaction->customer,
                            Notification::TRANSACTION,
                            $this->transaction->id
                        );
                        $this->transaction->order->OrderTransactions()->where('id','!=',$this->transaction->id)->update([
                            'status' => OrderTransaction::IS_CANCELED,
                        ]);
                        $this->emit('timer',['data' => $timer->toDateTimeString()]);
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    }
                    else
                        $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                    break;
                }
                case OrderTransaction::WAIT_FOR_PAY:{
                    if ((auth()->id() == $this->transaction->customer_id)) {
                        $commission = $this->transaction->commission;
                        $intermediary = $this->transaction->intermediary;
                        $price = $this->transaction->order->price + $commission/2 + $intermediary/2;
                        foreach ($this->form as $key => $item) {
                            if ($item['required'] == 1 && $item['for'] == 'customer' && $item['status'] == 'normal' && empty($this->transactionData[$item['name']]))
                                return $this->addError('transactionData.' . $item['name'] . '.error', __('validation.required', ['attribute' => 'اطلاعات']));
                        }
                        $data->value = json_encode($this->transactionData);
                        $data->save();
                        if ($price <= $this->transaction->customer->wallet->ballance){
                            try {
                                $this->transaction->customer->forceWithdraw($price, ['description' => $this->transaction->order->slug.'بابت معامله', 'from_admin'=> true]);
                            } catch (BalanceIsEmpty | InsufficientFunds $exception) {
                                return $this->addError('walletAmount', $exception->getMessage());
                            }
                            OrderTransactionPayment::create([
                                'orders_transactions_id' => $this->transaction->id,
                                'user_id' => auth()->id(),
                                'price' => $price,
                                'status' => OrderTransactionPayment::SUCCESS,
                                'gateway' => 'wallet',
                            ]);
                            $this->transaction->status = OrderTransaction::WAIT_FOR_SEND;
                            $timer = Carbon::make(now())->addMinutes(
                                (float)$this->transaction->order->category->send_time
                            );
                            $this->transaction->timer = $timer;
                            $sms->sends(
                                $this->createText('send_transaction',$this->transaction),
                                $this->transaction->seller,
                                Notification::TRANSACTION,
                                $this->transaction->id
                            );
                            $this->emit('timer',['data' => $timer->toDateTimeString()]);
                            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                        } else
                            $this->addError('transaction','موجودی کیف پول شما کمتر از مبلغ اگهی می باشد.');
                    } else
                        $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                    break;
                }
                case OrderTransaction::WAIT_FOR_SEND:{
                    if ((auth()->id() == $this->transaction->seller_id)) {
                        foreach ($this->form as $key => $item) {
                            if ($item['required'] == 1 && $item['for'] == 'seller' && $item['status'] == 'normal' && empty($this->transactionData[$item['name']]))
                                return $this->addError('transactionData.' . $item['name'] . '.error', __('validation.required', ['attribute' => 'اطلاعات']));
                        }
                        $data->value = json_encode($this->transactionData);
                        if ($this->transaction->order->category->type == Category::PHYSICAL) {
                            $this->validate([
                                'send_id' => ['required','exists:sends,id'],
                                'transfer_result' => ['required','string','max:250']
                            ],[],[
                                'send_id' => 'روش ارسال',
                                'transfer_result' => 'کد رهگیری'
                            ]);
                            $data->send_id = $this->send_id;
                            $data->transfer_result = $this->transfer_result;
                        }
                        $data->save();
                        $timer = '';
                        if ($this->transaction->order->category->control){
                            $this->transaction->status = OrderTransaction::WAIT_FOR_CONTROL;
                            $this->transaction->timer = '';
                            $sms->sends($this->createText('control_data',$this->transaction)
                                ,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                        } else {
                            $this->transaction->status = OrderTransaction::WAIT_FOR_RECEIVE;
                            if ($this->transaction->order->category->type == Category::DIGITAL)
                                $timer = Carbon::make(now())->addMinutes(
                                    (float)$this->transaction->order->category->receive_time
                                );
                            elseif ($this->transaction->order->category->type == Category::PHYSICAL) {
                                if ($this->transaction->order->city == $this->transaction->customer->city && $this->transaction->order->province == $this->transaction->customer->province)
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$this->transaction->data->send->send_time_inner_city
                                    );
                                else
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$this->transaction->data->send->send_time_outer_city
                                    );
                            }
                            $this->transaction->timer = $timer;
                            $sms->sends(
                                $this->createText('receive_transaction',$this->transaction),
                                $this->transaction->customer,
                                Notification::TRANSACTION,
                                $this->transaction->id
                            );
                        }
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    } else
                        $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                    break;
                }
                case OrderTransaction::WAIT_FOR_RECEIVE:{
                    if ((auth()->id() == $this->transaction->customer_id) || (auth()->id() == $this->transaction->seller_id && $timerStatus < 0)) {
                        $this->transaction->status = OrderTransaction::WAIT_FOR_TESTING;
                        $timer = Carbon::make(now())->addMinutes(
                            (float)$this->transaction->order->category->guarantee_time
                        );
                        $this->transaction->timer = $timer;
                        $sms->sends(
                            $this->createText('test_transaction',$this->transaction),
                            $this->transaction->seller,
                            Notification::TRANSACTION,
                            $this->transaction->id
                        );
                        if (auth()->id() == $this->transaction->seller_id){
                            $sms->sends(
                                $this->createText('skip_step',$this->transaction),
                                $this->transaction->customer,
                                Notification::TRANSACTION,
                                $this->transaction->id
                            );
                        }
                        $this->emit('timer',['data' => $timer->toDateTimeString()]);
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    } else
                        $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                    break;
                }
                case OrderTransaction::WAIT_FOR_NO_RECEIVE:{
                    if ((auth()->id() == $this->transaction->seller_id)) {
                        foreach ($this->form as $key => $item) {
                            if ($item['required'] == 1 && $item['for'] == 'seller' && $item['status'] == 'normal' && empty($this->transactionData[$item['name']]))
                                return $this->addError('transactionData.' . $item['name'] . '.error', __('validation.required', ['attribute' => 'اطلاعات']));
                        }
                        $data->value = json_encode($this->transactionData);
                        if ($this->transaction->order->category->type == Category::PHYSICAL) {
                            $this->validate([
                                'send_id' => ['required','exists:sends,id'],
                                'transfer_result' => ['required','string','max:250']
                            ],[],[
                                'send_id' => 'روش ارسال',
                                'transfer_result' => 'کد رهگیری'
                            ]);
                            $data->send_id = $this->send_id;
                            $data->transfer_result = $this->transfer_result;
                        }
                        $data->save();
                        $timer = '';
                        if ($this->transaction->order->category->control){
                            $this->transaction->status = OrderTransaction::WAIT_FOR_CONTROL;
                            $this->transaction->timer = '';
                            $sms->sends($this->createText('control_data',$this->transaction)
                                ,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                        } else {
                            $this->transaction->status = OrderTransaction::WAIT_FOR_RECEIVE;
                            if ($this->transaction->order->category->type == Category::DIGITAL)
                                $timer = Carbon::make(now())->addMinutes(
                                    (float)$this->transaction->order->category->receive_time
                                );
                            elseif ($this->transaction->order->category->type == Category::PHYSICAL) {
                                if ($this->transaction->order->city == $this->transaction->customer->city && $this->transaction->order->province == $this->transaction->customer->province)
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$this->transaction->data->send->send_time_inner_city
                                    );
                                else
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$this->transaction->data->send->send_time_outer_city
                                    );
                            }
                            $this->transaction->timer = $timer;
                            $sms->sends(
                                $this->createText('receive_transaction',$this->transaction),
                                $this->transaction->customer,
                                Notification::TRANSACTION,
                                $this->transaction->id
                            );
                        }
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    } elseif ((auth()->id() == $this->transaction->customer_id && $timerStatus < 0)){
                        $this->backMoney();
                        OrderTransactionData::where('transaction_id',$this->transaction->id)->update([
                            'value'=> null
                        ]);
                        $texts = $this->createText('cancel_transaction',$this->transaction);
                        $sms->sends($texts,$this->transaction->seller);
                        $sms->sends($texts,$this->transaction->customer);
                        $this->emitNotify('معامله با موفقیت کنسل شد.');
                        $this->transaction->status = OrderTransaction::IS_CANCELED;
                        $this->transaction->order->status = Order::IS_CONFIRMED;
                        $this->transaction->order->save();
                    }
                }
                case OrderTransaction::WAIT_FOR_TESTING:{
                    if ((auth()->id() == $this->transaction->customer_id) || (auth()->id() == $this->transaction->seller_id && $timerStatus < 0)) {
                        $this->transaction->status = OrderTransaction::WAIT_FOR_COMPLETE;
                        $text = $this->createText('complete_transaction',$this->transaction);
                        $this->transaction->order->status = Order::IS_FINISHED;
                        $this->transaction->order->save();
                        $sms->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                        $sms->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                        if (auth()->id() == $this->transaction->seller_id){
                            $sms->sends(
                                $this->createText('skip_step',$this->transaction),
                                $this->transaction->customer,
                                Notification::TRANSACTION,
                                $this->transaction->id
                            );
                        }
                        if (!empty($this->transaction->payment) && $this->transaction->payment->status == OrderTransactionPayment::SUCCESS){
                            $price = $this->transaction->payment->price;
                            $commission = $this->transaction->commission;
                            $intermediary = $this->transaction->intermediary;
                            $final_price = ($price - ($commission) - ($intermediary));
                            $this->transaction->seller->deposit($final_price,
                                ['description' => $this->transaction->code.'واریز هزینه بابت معامله به کد ', 'from_admin'=> true]);
                        }
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    } else
                        $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                    break;
                }
            }
        } elseif ($this->transaction->is_returned) {
            switch ($this->transaction->status)
            {
                case OrderTransaction::WAIT_FOR_SENDING_DATA:{
                    if ((auth()->id() == $this->transaction->seller_id)) {
                        foreach ($this->form as $key => $item) {
                            if ($item['required'] == 1 && $item['for'] == 'seller' && $item['status'] == 'return' && empty($this->transactionData[$item['name']]))
                                return $this->addError('transactionData.' . $item['name'] . '.error', __('validation.required', ['attribute' => 'اطلاعات']));
                        }
                        $data->value = json_encode($this->transactionData);
                        $data->save();
                        $this->transaction->status = OrderTransaction::WAIT_FOR_SEND;
                        $text = $this->createText('returned_send_transaction',$this->transaction);
                        $model = $this->transaction->customer;
                        $sms->sends($text,$model,Notification::TRANSACTION,$this->transaction->id);
                    } elseif ((auth()->id() == $this->transaction->customer_id && $timerStatus < 0)){
                        $this->backMoney();
                        OrderTransactionData::where('transaction_id',$this->transaction->id)->update([
                            'value'=> null
                        ]);
                        $texts = $this->createText('cancel_transaction',$this->transaction);
                        $sms->sends($texts,$this->transaction->seller);
                        $sms->sends($texts,$this->transaction->customer);
                        $this->emitNotify('معامله با موفقیت کنسل شد.');
                        $this->transaction->status = OrderTransaction::IS_CANCELED;
                        $this->transaction->order->status = Order::IS_FINISHED;
                        $this->transaction->order->save();
                        $sms->sends($this->createText('skip_step',$this->transaction)
                            ,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                    }
                    break;
                }
                case OrderTransaction::WAIT_FOR_SEND:{
                    if ((auth()->id() == $this->transaction->customer_id)) {
                        foreach ($this->form as $key => $item) {
                            if ($item['required'] == 1 && $item['for'] == 'customer' && $item['status'] == 'return' && empty($this->transactionData[$item['name']]))
                                return $this->addError('transactionData.' . $item['name'] . '.error', __('validation.required', ['attribute' => 'اطلاعات']));
                        }
                        $data->value = json_encode($this->transactionData);
                        if ($this->transaction->order->category->type == Category::PHYSICAL) {
                            $this->validate([
                                'send_id' => ['required','exists:sends,id'],
                                'transfer_result' => ['required','string','max:250']
                            ],[],[
                                'send_id' => 'روش ارسال',
                                'transfer_result' => 'کد رهگیری'
                            ]);
                            $data->send_id = $this->send_id;
                            $data->transfer_result = $this->transfer_result;
                        }
                        $data->save();
                        $timer = '';
                        if ($this->transaction->order->category->control){
                            $this->transaction->status = OrderTransaction::WAIT_FOR_CONTROL;
                            $this->transaction->timer = '';
                            $sms->sends($this->createText('control_data',$this->transaction)
                                ,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                        } else {
                            $this->transaction->status = OrderTransaction::WAIT_FOR_RECEIVE;
                            if ($this->transaction->order->category->type == Category::DIGITAL)
                                $timer = Carbon::make(now())->addMinutes(
                                    (float)$this->transaction->order->category->receive_time
                                );
                            elseif ($this->transaction->order->category->type == Category::PHYSICAL) {
                                if ($this->transaction->order->city == $this->transaction->customer->city && $this->transaction->order->province == $this->transaction->customer->province)
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$this->transaction->data->send->send_time_inner_city
                                    );
                                else
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$this->transaction->data->send->send_time_outer_city
                                    );
                            }
                            $this->transaction->timer = $timer;
                            $sms->sends(
                                $this->createText('returned_receive_transaction',$this->transaction),
                                $this->transaction->seller,
                                Notification::TRANSACTION,
                                $this->transaction->id
                            );
                        }
                    } elseif ((auth()->id() == $this->transaction->seller_id && $timerStatus < 0)){
                        $this->transaction->status = OrderTransaction::WAIT_FOR_COMPLETE;
                        $this->transaction->is_returned = 0;
                        $text = $this->createText('complete_transaction',$this->transaction);
                        $this->transaction->order->status = Order::IS_FINISHED;
                        $this->transaction->order->save();
                        $sms->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                        $sms->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                        if (!empty($this->transaction->payment) && $this->transaction->payment->status == OrderTransactionPayment::SUCCESS){
                            $price = $this->transaction->payment->price;
                            $commission = $this->transaction->commission;
                            $intermediary = $this->transaction->intermediary;
                            $final_price = ($price - ($commission) - ($intermediary));
                            $this->transaction->seller->deposit($final_price,
                                ['description' => $this->transaction->code.'واریز هزینه بابت معامله به کد ', 'from_admin'=> true]);
                        }
                        $sms->sends($this->createText('skip_step',$this->transaction)
                            ,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                    }
                    break;
                }
                case OrderTransaction::WAIT_FOR_RECEIVE:{
                    if ((auth()->id() == $this->transaction->seller_id) || (auth()->id() == $this->transaction->customer_id && $timerStatus < 0) ) {
                        $this->transaction->status = OrderTransaction::IS_CANCELED;
                        $text = $this->createText('cancel_transaction',$this->transaction);
                        $model = $this->transaction->customer;
                        $sms->sends($text,$model,Notification::TRANSACTION,$this->transaction->id);
                        if (auth()->id() == $this->transaction->customer_id){
                            $sms->sends(
                                $this->createText('skip_step',$this->transaction),
                                $this->transaction->seller,
                                Notification::TRANSACTION,
                                $this->transaction->id
                            );
                        }
                        $this->transaction->order->status = Order::IS_CONFIRMED;
                        $this->transaction->order->save();
                        $this->backMoney();
                        OrderTransactionData::where('transaction_id',$this->transaction->id)->update([
                            'value'=> null
                        ]);
                    }
                    break;
                }
                case OrderTransaction::WAIT_FOR_NO_RECEIVE:{
                    if ((auth()->id() == $this->transaction->customer_id)) {
                        foreach ($this->form as $key => $item) {
                            if ($item['required'] == 1 && $item['for'] == 'customer' && $item['status'] == 'return' && empty($this->transactionData[$item['name']]))
                                return $this->addError('transactionData.' . $item['name'] . '.error', __('validation.required', ['attribute' => 'اطلاعات']));
                        }
                        $data->value = json_encode($this->transactionData);
                        if ($this->transaction->order->category->type == Category::PHYSICAL) {
                            $this->validate([
                                'send_id' => ['required','exists:sends,id'],
                                'transfer_result' => ['required','string','max:250']
                            ],[],[
                                'send_id' => 'روش ارسال',
                                'transfer_result' => 'کد رهگیری'
                            ]);
                            $data->send_id = $this->send_id;
                            $data->transfer_result = $this->transfer_result;
                        }
                        $data->save();
                        $timer = '';
                        if ($this->transaction->order->category->control){
                            $this->transaction->status = OrderTransaction::WAIT_FOR_CONTROL;
                            $this->transaction->timer = '';
                            $sms->sends($this->createText('control_data',$this->transaction)
                                ,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                        } else {
                            $this->transaction->status = OrderTransaction::WAIT_FOR_RECEIVE;
                            if ($this->transaction->order->category->type == Category::DIGITAL)
                                $timer = Carbon::make(now())->addMinutes(
                                    (float)$this->transaction->order->category->receive_time
                                );
                            elseif ($this->transaction->order->category->type == Category::PHYSICAL) {
                                if ($this->transaction->order->city == $this->transaction->customer->city && $this->transaction->order->province == $this->transaction->customer->province)
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$this->transaction->data->send->send_time_inner_city
                                    );
                                else
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$this->transaction->data->send->send_time_outer_city
                                    );
                            }
                            $this->transaction->timer = $timer;
                            $sms->sends(
                                $this->createText('receive_transaction',$this->transaction),
                                $this->transaction->customer,
                                Notification::TRANSACTION,
                                $this->transaction->id
                            );
                        }
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    } elseif ((auth()->id() == $this->transaction->seller_id && $timerStatus < 0)){
                        $this->transaction->status = OrderTransaction::WAIT_FOR_COMPLETE;
                        $this->transaction->is_returned = 0;
                        $text = $this->createText('complete_transaction',$this->transaction);
                        $this->transaction->order->status = Order::IS_FINISHED;
                        $this->transaction->order->save();
                        $sms->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                        $sms->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                        if (!empty($this->transaction->payment) && $this->transaction->payment->status == OrderTransactionPayment::SUCCESS){
                            $price = $this->transaction->payment->price;
                            $commission = $this->transaction->commission;
                            $intermediary = $this->transaction->intermediary;
                            $final_price = ($price - ($commission) - ($intermediary));
                            $this->transaction->seller->deposit($final_price,
                                ['description' => $this->transaction->code.'واریز هزینه بابت معامله به کد ', 'from_admin'=> true]);
                        }
                        $sms->sends($this->createText('skip_step',$this->transaction)
                            ,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                    }
                }
            }
        }
        $this->transaction->save();
        return(false);
    }

    public function cancel()
    {
        $timerStatus = Carbon::make(now())->diff($this->transaction->timer)->format('%r%s');
        $data = $this->transaction->data;
        $sms = new SendMessages();
        switch ($this->transaction->status)
        {
            case OrderTransaction::WAIT_FOR_CONFIRM:{
                $this->transaction->status = OrderTransaction::IS_CANCELED;
                $texts = $this->createText('cancel_transaction',$this->transaction);
                $sms->sends($texts,$this->transaction->seller);
                $sms->sends($texts,$this->transaction->customer);
                $this->backMoney();
                $this->emitNotify('معامله با موفقیت کنسل شد.');
                OrderTransactionData::where('transaction_id',$this->transaction->id)->update([
                    'value'=> null
                ]);
                break;
            }
            case OrderTransaction::WAIT_FOR_PAY:{
                if ((auth()->id() == $this->transaction->customer_id) || (auth()->id() == $this->transaction->seller_id && $timerStatus < 0)) {
                    $this->transaction->status = OrderTransaction::IS_CANCELED;
                    $texts = $this->createText('cancel_transaction',$this->transaction);
                    $sms->sends($texts,$this->transaction->seller);
                    $sms->sends($texts,$this->transaction->customer);
                    $this->backMoney();
                    $this->emitNotify('معامله با موفقیت کنسل شد.');
                    OrderTransactionData::where('transaction_id',$this->transaction->id)->update([
                        'value'=> null
                    ]);
                    break;
                }
            }
            case OrderTransaction::WAIT_FOR_SEND:{
                if ((auth()->id() == $this->transaction->seller_id) || (auth()->id() == $this->transaction->customer_id && $timerStatus < 0)) {
                    $this->transaction->status = OrderTransaction::IS_CANCELED;
                    $texts = $this->createText('cancel_transaction',$this->transaction);
                    $sms->sends($texts,$this->transaction->seller);
                    $sms->sends($texts,$this->transaction->customer);
                    $this->backMoney();
                    $this->emitNotify('معامله با موفقیت کنسل شد.');
                    OrderTransactionData::where('transaction_id',$this->transaction->id)->update([
                        'value'=> null
                    ]);
                    break;
                }
            }
        }
        $this->transaction->order->status = Order::IS_CONFIRMED;
        $this->transaction->order->save();
        $this->transaction->save();
        return(false);
    }

    public function payMore()
    {
        $this->validate([
            'price' => ['required','numeric','between:1000,40000000'],
            'gateway' => ['requred','in:payir,zarinpal'],
        ],[],[
            'price'=> 'مبلغ',
            'gateway' => 'درگاه',
        ]);
        try {
            $payment = Payment::via($this->gateway)->callbackUrl(env('APP_URL') . '/verify/'. $this->gateway)
                ->purchase((new Invoice)
                    ->amount(($this->price)), function ($driver,$transactionId) {
                    $this->storePay($this->gateway ,$transactionId);
                })->pay()->toJson();
            $payment = json_decode($payment);
            return redirect($payment->action);
        } catch (PurchaseFailedException $exception) {
            $this->addError('payment', $exception->getMessage());
        }
        return(false);
    }

    public function storePay($gateway , $transactionId)
    {
        return  DB::transaction(function () use ($gateway, $transactionId) {
            if (!is_null($gateway)) {
                $pay = Pay::create([
                    'amount' => $this->price,
                    'payment_gateway' => $gateway,
                    'payment_token' => $transactionId,
                    'model_type' => 'user',
                    'model_id' => auth()->id(),
                    'status_code' => 8,
                    'user_id' => auth()->id(),
                    'call_back_url' => url()->current(),
                ]);
                return $pay->id;
            }
            return false;
        });
    }

    public function no_receive()
    {
        if ($this->transaction->status == OrderTransaction::WAIT_FOR_RECEIVE){
            if ($this->transaction->customer_id == auth()->id()) {
                $this->validate([
                    'received_result' => ['required','string','max:2000']
                ],[],[
                    'received_result' => 'متن توضیحات',
                ]);
                $sms = new SendMessages();
                $timer = (float)$this->transaction->order->category->no_receive_time;
                $this->transaction->received_status = true;
                $this->transaction->received_result = $this->received_result;
                $this->transaction->timer = Carbon::make(now())->addMinutes($timer);
                $this->transaction->status = OrderTransaction::WAIT_FOR_NO_RECEIVE;
                $this->transaction->save();
                $target = $this->transaction->is_returned ? $this->transaction->customer : $this->transaction->seller;
                $sms->sends(
                    $this->createText('no_receive_transaction',$this->transaction),
                    $target,
                    Notification::TRANSACTION,
                    $this->transaction->id
                );
                $this->reset(['received_result']);
                return $this->emitNotify('اطلاعات با موفقیت ثبت شد لطفا تا اطلاع مدیریت منتظر بمانید');
            } else
                return $this->addError('error','شما اجازه این کار را ندارید');
        } else
            return $this->addError('error','شما اجازه این کار را ندارید');
    }

    public function requestToReturn()
    {
        if (!$this->transaction->is_returned && $this->transaction->status == OrderTransaction::WAIT_FOR_TESTING){
            if ($this->transaction->customer_id == auth()->id()){
                $sms = new SendMessages();
                $this->validate([
                    'return_cause' => ['required','string','max:2000'],
                    'return_images' => ['array','min:1','max:3'],
                    'return_images.*' => ['required','image','mimes:'.Setting::getSingleRow('valid_order_images'),'max:'.Setting::getSingleRow('max_order_image_size')],
                ],[],
                [
                    'return_cause' => 'متن توضیحات',
                    'return_images' => 'تصاویر',
                ]);
                if (!is_null($this->return_images)) {
                    $gallery = [];
                    foreach ($this->return_images as $image) {
                        $pic = 'storage/'.$image->store('files/returns', 'public').',';
                        array_push($gallery,$pic);
                    }
                    $gallery = implode(',',$gallery);
                } else
                    return false;
                $this->transaction->status = OrderTransaction::IS_RETURNED;
                $this->transaction->return_cause = $this->return_cause;
                $this->transaction->return_images = $gallery;
                $this->transaction->save();
                $sms->sends(
                    $this->createText('request_to_return_transaction',$this->transaction),
                    $this->transaction->seller,
                    Notification::TRANSACTION,
                    $this->transaction->id
                );
                $this->reset(['return_cause','return_images']);
                return $this->emitNotify('اطلاعات با موفقیت ثبت شد لطفا تا اطلاع مدیریت منتظر بمانید');
            } else
                return $this->addError('error','شما اجازه این کار را ندارید');
        } else
            return $this->addError('error','شما اجازه این کار را ندارید');
    }

    public function backMoney()
    {
        if (!empty($this->transaction->payment) && $this->transaction->payment->status == OrderTransactionPayment::SUCCESS){
            $price = $this->transaction->payment->price;
            $this->transaction->customer->deposit($price,
                ['description' => $this->transaction->code.'بازگشت هزینه بابت معامله به کد ', 'from_admin'=> true]);
        }
    }
}

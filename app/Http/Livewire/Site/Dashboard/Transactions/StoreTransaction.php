<?php

namespace App\Http\Livewire\Site\Dashboard\Transactions;

use App\Http\Livewire\BaseComponent;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderTransactionPayment;
use App\Models\Payment;
use App\Models\OrderTransaction;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Carbon\Carbon;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment as Pay;

class StoreTransaction extends BaseComponent
{
    use TextBuilder;
    public $transaction , $status ,$timer , $form = []  , $transactionData = [] , $transactionDataTransfer = [];
    public  $data = [] , $return , $chat , $message  , $received_result , $finalPrice , $send_id , $transfer_result;
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
                        $data->save();

                        if ($this->transaction->order->category->control){
                            $this->transaction->status = OrderTransaction::WAIT_FOR_CONTROL;
                            $this->transaction->timer = '';
                            $this->emit('timer',['data' => '']);
                        } else {
                            $this->transaction->status = OrderTransaction::WAIT_FOR_RECEIVE;
                            $timer = Carbon::make(now())->addMinutes(
                                (float)$this->transaction->order->category->receive_time
                            );
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
                        $this->emit('timer',['data' => $timer->toDateTimeString()]);
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    } else
                        $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                    break;
                }
                case OrderTransaction::WAIT_FOR_TESTING:{
                    if ((auth()->id() == $this->transaction->customer_id) || (auth()->id() == $this->transaction->seller_id && $timerStatus < 0)) {
                        $this->transaction->status = OrderTransaction::WAIT_FOR_COMPLETE;
                        $text = $this->createText('complete_transaction',$this->transaction);
                        $this->transaction->order->status = Order::IS_FINISHED;
                        $this->transaction->order->save();
                        $sms->sends($text,$this->transaction->seller,Notification::TRANSACTION,$this->transaction->id);
                        $sms->sends($text,$this->transaction->customer,Notification::TRANSACTION,$this->transaction->id);
                        if (!empty($this->transaction->payment) && $this->transaction->payment->status == OrderTransactionPayment::SUCCESS){
                            $price = $this->transaction->order->price;
                            $commission = $this->transaction->commission;
                            $intermediary = $this->transaction->intermediary;
                            $final_price = ($price - ($commission/2) - ($intermediary/2));
                            $this->transaction->seller->deposit($final_price,
                                ['description' => $this->transaction->code.'بابت معامله به کد ', 'from_admin'=> true]);
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
                    break;
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
        if (in_array($this->transaction->status,[OrderTransaction::IS_CONFIRMED,OrderTransaction::IS_SENT])) {
            if ((auth()->id() == $this->transaction->seller_id) || (auth()->id() == $this->transaction->customer_id && $timerStatus < 0)) {
                $this->transaction->status = OrderTransaction::IS_CANCELED;
                $texts = $this->createText('cancel_transaction',$this->transaction);
                $sms->sends($texts,$this->transaction->seller);
                $sms->sends($texts,$this->transaction->customer);
                $this->backMoney();
                $this->emitNotify('معامله با موفقیت کنسل شد.');
            }
        } elseif (in_array($this->transaction->status,[OrderTransaction::IS_PAID])) {
            if ((auth()->id() == $this->transaction->customer_id) || (auth()->id() == $this->transaction->seller_id && $timerStatus < 0)) {
                $this->transaction->status = OrderTransaction::IS_CANCELED;
                $texts = $this->createText('cancel_transaction',$this->transaction);
                $sms->sends($texts,$this->transaction->seller);
                $sms->sends($texts,$this->transaction->customer);
                $this->backMoney();
                $this->emitNotify('معامله با موفقیت کنسل شد.');
            }
        } elseif (in_array($this->transaction->status , [OrderTransaction::IS_TESTING,OrderTransaction::IS_RECEIVED]) && auth()->id() == $this->transaction->customer_id) {
            if ((is_null($data) || is_null($data->value)) && $timerStatus < 0) {
                $this->transaction->status = OrderTransaction::IS_CANCELED;
                $texts = $this->createText('cancel_transaction',$this->transaction);
                $sms->sends($texts,$this->transaction->seller);
                $sms->sends($texts,$this->transaction->customer);
                $this->backMoney();
                $this->emitNotify('معامله با موفقیت کنسل شد.');
            } elseif($timerStatus < 0 && (!is_null($data) || !is_null($data->value))) {
                $this->validate([
                    'received_result' => ['required','string','max:250']
                ],[],[
                    'received_result' => 'توضیحات'
                ]);
                $this->transaction->received_result = $this->received_result;
                $this->transaction->received_status = true;
                $this->transaction->status = OrderTransaction::CONTROL;
                $this->emitNotify('در انتظار کنترل');
            }
        }
        $this->transaction->save();
        return(false);
    }

    public function payMore()
    {
        if ($this->finalPrice >= 1000)
        {
            try {
                $payment = Pay::callbackUrl(env('APP_URL') . '/call-back')
                    ->purchase((new Invoice)
                        ->amount(($this->finalPrice)), function ($driver,$transactionId) {

                    })->pay()->toJson();
                $payment = json_decode($payment);
                return redirect($payment->action);
            } catch (PurchaseFailedException $exception) {
                $this->addError('payment', $exception->getMessage());
            }
        } else
            $this->addError('payment', 'حداقل مبلغ برای پرداخت 1000 تومان می باشد');
    }

    public function no_receive_customer()
    {

    }

    public function no_receive_seller()
    {

    }

    public function requestToReturn()
    {

    }


}

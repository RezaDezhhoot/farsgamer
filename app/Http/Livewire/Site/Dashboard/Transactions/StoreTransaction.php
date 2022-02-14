<?php

namespace App\Http\Livewire\Site\Dashboard\Transactions;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderTransaction;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Carbon\Carbon;
use Livewire\Component;
use Morilog\Jalali\Jalalian;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment as Pay;

class StoreTransaction extends Component
{
    use TextBuilder;
    public $transaction , $status ,$timer , $form = []  , $transactionData = [] , $transactionDataTransfer = [];
    public  $data = [] , $return , $chat , $message  , $received_result , $finalPrice;

    public function mount($action , $id){
        if ($action == 'edit') {
            $this->transaction = OrderTransaction::where('seller_id',auth()->id())->orWhere('customer_id',auth()->id())
                ->findOrFail($id);
            $this->form = json_decode($this->transaction->order->category->forms,true) ?? [];
            $this->message = $this->transaction->messages;
            $this->transactionData = $this->transaction->data ? json_decode($this->transaction->data['value'],true) : [];
            $this->chat = $this->transaction->chatGroup();
            $this->status = $this->transaction->status;
        } else abort(404);
    }

    public function render()
    {
        foreach ($this->transaction::getTransactionStatus($this->return) as $key => $item)
            $this->data['status'][$key] = $item['label'];

        $this->store();
        return view('livewire.site.dashboard.order-transactions.store-transaction');
    }

    public function store()
    {
        $timerStatus = Carbon::make(now())->diff($this->transaction->timer)->format('%r%s');
        $data = $this->transaction->data;
        $sms = new SendMessages();
        switch ($this->transaction->status)
        {
            case OrderTransaction::IS_CONFIRMED:{
                if ((auth()->id() == $this->transaction->seller_id)) {
                    $this->transaction->status = OrderTransaction::IS_PAID;
                    $timer = Carbon::make(now())->addHours(
                        (float)OrderTransaction::getTimer(OrderTransaction::IS_PAID)
                    );
                    $this->transaction->timer = $timer;
                    $sms->sends(
                        $this->createText('pay_transaction',$this->transaction),
                        $this->transaction->customer
                    );
                    $this->emit('timer',['data' => $timer->toDateTimeString()]);
                    $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                }
                else
                    $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                break;
            }
            case OrderTransaction::IS_PAID:{
                if ((auth()->id() == $this->transaction->customer_id)) {
                    $commission = $this->transaction->commission;
                    $intermediary = $this->transaction->intermediary;
                    $price = $this->transaction->order->price + $commission + $intermediary;
                    if ($price <= $this->transaction->customer->wallet->due_amount){
                        $this->transaction->customer->wallet->charge($price,'deduction');
                        Payment::create([
                            'user_id' => $this->transaction->seller->id,
                            'user_ip' => request()->ip(),
                            'json' => '',
                            'price' => $price,
                            'track_id' => 0,
                            'gateway' => 'wallet',
                            'status' => Payment::SUCCESS,
                            'transaction_id' => $this->transaction->id,
                            'payment_for' => 'transaction',
                            'case_id' => $this->transaction->id,
                        ]);
                        $this->transaction->status = OrderTransaction::IS_SENT;
                        $timer = Carbon::make(now())->addHours(
                            (float)$this->transaction->order->category->send_time
                        );
                        $this->transaction->timer = $timer;
                        $sms->sends(
                            $this->createText('send_transaction',$this->transaction),
                            $this->transaction->seller
                        );
                        $this->emit('timer',['data' => $timer->toDateTimeString()]);
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    } else
                        $this->addError('transaction','موجودی کیف پول شما کمتر از مبلغ اگهی می باشد.');
                } else
                    $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                break;
            }
            case OrderTransaction::IS_SENT:{
                if ((auth()->id() == $this->transaction->seller_id)) {
                    if (!in_array($this->transaction->status,[OrderTransaction::IS_COMPLETED,OrderTransaction::IS_REQUESTED,OrderTransaction::IS_CANCELED])) {
                        if (!is_null($data)) {
                            foreach ($this->form as $key => $item) {
                                if ($item['required'] == 1 && empty($this->transactionData[$item['name']]))
                                    $this->addError('transactionData.' . $item['name'] . '.error', __('validation.required', ['attribute' => 'اطلاعات']));
                            }
                        }
                        if (sizeof($this->getErrorBag()) > 0)
                            return $this->addError('transaction', 'موارد خواسته شده را تکمیل کنید.');

                        $data->value = json_encode($this->transactionData);
                        $data->save();
                        if ($this->transaction->order->category->control == 1){
                            $this->transaction->status = OrderTransaction::CONTROL;
                            $this->transaction->timer = '';
                            $this->emit('timer',['data' => '']);
                        } else {
                            $this->transaction->status = OrderTransaction::IS_RECEIVED;
                            $timer = Carbon::make(now())->addHours(
                                (float)$this->transaction->order->category->send_time
                            );
                            $this->transaction->timer = $timer;
                            $sms->sends(
                                $this->createText('receive_transaction',$this->transaction),
                                $this->transaction->customer
                            );
                        }
                        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                    }
                } else
                    $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                break;
            }
            case OrderTransaction::IS_RECEIVED:{
                if ((auth()->id() == $this->transaction->customer_id) || (auth()->id() == $this->transaction->seller_id && $timerStatus < 0)) {
                    $this->transaction->status = OrderTransaction::IS_TESTING;
                    $timer = Carbon::make(now())->addHours(
                        (float)$this->transaction->order->category->guarantee_time
                    );
                    $this->transaction->timer = $timer;
                    $sms->sends(
                        $this->createText('test_transaction',$this->transaction),
                        $this->transaction->seller
                    );
                    $this->emit('timer',['data' => $timer->toDateTimeString()]);
                    $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                } else
                    $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                break;
            }
            case OrderTransaction::IS_TESTING:{
                if ((auth()->id() == $this->transaction->customer_id) || (auth()->id() == $this->transaction->seller_id && $timerStatus < 0)) {
                    $this->transaction->status = OrderTransaction::IS_COMPLETED;
                    $text = $this->createText('complete_transaction',$this->transaction);
                    $this->transaction->order->status = Order::IS_FINISHED;
                    $this->transaction->order->save();
                    $sms->sends($text,$this->transaction->seller);
                    $sms->sends($text,$this->transaction->customer);
                    if (!empty($this->transaction->payment) && $this->transaction->payment->status == Payment::SUCCESS){
                        $price = $this->transaction->order->price;
                        $commission = $this->transaction->commission;
                        $intermediary = $this->transaction->intermediary;
                        $final_price = ($price - $commission - $intermediary) > 0 ? $price - $commission - $intermediary : 0;
                        $this->transaction->seller->wallet->charge($final_price,'addition');
                    }
                    $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                } else
                    $this->addError('transaction','شما اجازه برای این عملیات را ندارید.');
                break;
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

    public function savePay($driver,$transactionId)
    {

    }

    public function backMoney()
    {
        if (!empty($this->transaction->payment) && $this->transaction->payment->status == Payment::SUCCESS){
            $price = $this->transaction->order->price;
            $commission = $this->transaction->commission;
            $intermediary = $this->transaction->intermediary;
            $final_price = $price + $commission + $intermediary;
            $this->transaction->customer->wallet->charge($final_price,'addition');
        }
    }

    public function setTimer()
    {
        $this->emit('timer',['data' => $this->transaction->timer]);
    }
}

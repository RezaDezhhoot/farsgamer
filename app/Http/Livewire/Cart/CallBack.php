<?php

namespace App\Http\Livewire\Cart;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Payment as Pay;
use Illuminate\Support\Facades\Redirect;

class CallBack extends BaseComponent
{
    public $data;
    public $isSuccessful, $message;
    public $gateway , $user;
    public $Authority, $Status, $status, $token, $tracking , $link;
    protected $queryString = ['gateway', 'Authority', 'Status', 'token', 'status', 'tracking'];
    public function mount(PaymentRepositoryInterface $paymentRepository , $gateway)
    {
        $this->getData($paymentRepository);
        if (!empty($this->gateway)){
            try {
                if ($this->gateway == 'payir') {
                    $payment = Payment::via($this->gateway)->amount($this->data->amount)->transactionId($this->token)->verify();
                } else {
                    $payment = Payment::via($this->gateway)->amount($this->data->amount)->transactionId($this->Authority)->verify();
                }
                return $this->success($paymentRepository,$payment);

            } catch (InvalidPaymentException $exception) {
                $pay = '';
                if ($this->gateway == 'payir') {
                    $pay = Pay::where('payment_token', $this->token)->first();
                } else {
                    $pay = Pay::where('payment_token', $this->Authority)->first();
                }
                $pay->update([
                    'status_code' => $exception->getCode(),
                    'status_message' => $exception->getMessage(),
                ]);
                $this->isSuccessful = false;
                $this->link = $pay->call_back_url."?status_code=$pay->status_code&message=$pay->status_message";
                return Redirect::to($this->link)->with(['status_code' =>$pay->status_code,'message' => $pay->status_message]);
            }
        } abort(404);
    }

    private function success(PaymentRepositoryInterface $paymentRepository,$payment = null)
    {
        $this->isSuccessful = true;
        $pay = '';


        if (!is_null($payment) && !Pay::where('payment_ref', $payment->getReferenceId())->exists()) {
            if ($this->gateway == 'payir') {
                $pay = Pay::where('payment_token', $this->token)->first();
            } else {
                $pay = Pay::where('payment_token', $this->Authority)->first();
            }
            $pay->update([
                'payment_ref' => $payment->getReferenceId(),
                'status_code' => '100',
                'status_message' => 'پرداخت با موفقیت انجام شد',
            ]);
            $this->data->user->deposit($pay->amount, ['description' =>  'پرداخت وجه' , 'from_admin'=> true]);

        } else {
            if ($this->gateway == 'payir') {
                $pay = Pay::where('payment_token', $this->token)->first();
            } else {
                $pay = Pay::where('payment_token', $this->Authority)->first();
            }
        }
        $this->link = $pay->call_back_url."?status_code=$pay->status_code&message=$pay->status_message";
        return Redirect::to($this->link)->with(['status_code' =>$pay->status_code,'message' => $pay->status_message]);
    }


    public function render()
    {
        return view('livewire.cart.call-back');
    }

    private function getData(PaymentRepositoryInterface $paymentRepository)
    {
        if ($this->gateway == 'zarinpal') {
            $transaction = Pay::where('payment_gateway', 'zarinpal')
                ->where('payment_token', $this->Authority)
                ->where('model_type', 'user')
                ->firstOrFail();

            $this->data = $transaction;
        } elseif ($this->gateway == 'payir') {
            $transaction = Pay::where('payment_gateway', 'payir')
                ->where('payment_token', $this->token)
                ->where('model_type', 'user')
                ->firstOrFail();
            $this->data = $transaction;
        }
    }
}

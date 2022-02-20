<?php

namespace App\Http\Livewire\Site\Dashboard\Accounting;

use App\Http\Livewire\BaseComponent;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\Request;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use App\Models\Payment as Pay;
class IndexAccounting extends BaseComponent
{
    use WithPagination;
    protected $queryString = ['status','code','payStatus'];
    public $status , $pagination = 10 , $data = [] , $price , $gateway = 'pay' , $code  , $message;

    public function mount()
    {
        $this->data['status'] = Request::getStatus();
        $this->data['status'][Request::SETTLEMENT].= '('.Request::where('status',Request::SETTLEMENT)->count().')';
        $this->data['status'][Request::NEW].= '('.Request::where('status',Request::NEW)->count().')';
        if (isset($this->code))
            $this->message = Pay::find($this->code)->status_message;
    }

    public function render()
    {
        $requests = auth()->user()->requests()->latest('id')->with(['user'])->when($this->status, function ($query){
            return $query->where('status',$this->status);
        })->paginate($this->pagination);
        return view('livewire.site.dashboard.accountings.index-accounting',['requests'=>$requests]);
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
                    $this->store($this->gateway ,$transactionId);
                })->pay()->toJson();
            $payment = json_decode($payment);
            return redirect($payment->action);
        } catch (PurchaseFailedException $exception) {
            $this->addError('payment', $exception->getMessage());
        }
    }

    private function store($gateway = null, $transactionId = null)
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
                ]);
                return $pay->id;
            }
        });
    }
}

<?php

namespace App\Http\Livewire\Site\Orders;

use App\Cart\Facades\Cart;
use App\Http\Livewire\BaseComponent;
use App\Models\Notification;
use App\Models\Save;
use App\Models\Setting;
use App\Models\OrderTransaction;
use App\Models\OrderTransactionData;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;


class SingleOrder extends BaseComponent
{
    use TextBuilder;
    public $order , $data = [] , $confirmLaw = false , $law;
    public function mount($userID, $id , $slug)
    {
        $this->order = Order::where([
            ['status',Order::IS_CONFIRMED],
            ['user_id',$userID],
        ])->findOrFail($id);

        Cart::add(\App\Cart\Cart::LAST_VIEW,$this->order);
        $this->order->increment('view_count',1);
        $this->data['pageAddress'] = [
            Setting::getSingleRow('title') => route('home'),
            $this->order->category->title => route('home',['category' =>$this->order->category->slug]),
            $this->order->slug => '',
        ];
        $category = $this->order->category;
        SEOMeta::setTitle($category->title.' | '.$this->order->slug,false);
        SEOMeta::setDescription($category->seo_description);
        SEOMeta::addKeyword(explode(',',$category->seo_keywords));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle($category->title.' | '.$this->order->slug);
        OpenGraph::setDescription($category->seo_description);
        TwitterCard::setTitle($category->title.' | '.$this->order->slug);
        TwitterCard::setDescription($category->seo_description);
        JsonLd::setTitle($category->title.' | '.$this->order->slug);
        JsonLd::setDescription($category->seo_description);
        JsonLd::addImage(Setting::getSingleRow('logo'));
    }

    public function sendRequestToTransaction()
    {
        if (Auth::check()) {
            if ($this->order->status == Order::IS_CONFIRMED && $this->order->user->id != \auth()->id()) {
                $ban = Carbon::make(now())->diff(\auth()->user()->ban)->format('%r%i');
                if ($ban <= 0) {
                    $this->validate([
                        'confirmLaw' => ['required'],
                    ],[],[
                        'confirmLaw' => 'تایید قوانین',
                    ]);
                    $transaction = new OrderTransaction();
                    $transaction->customer_id = Auth::id();
                    $transaction->seller_id = $this->order->user_id;
                    $transaction->order_id = $this->order->id;
                    $transaction->status = OrderTransaction::WAIT_FOR_CONFIRM;
                    $transaction->timer = Carbon::make(now())->addHours(0);
                    $transaction->save();
                    OrderTransactionData::updateOrCreate(['transaction_id'=>$transaction->id],['name'=>uniqid()]);
                    $texts = $this->createText('confirm_transaction',$transaction);
                    $send = new SendMessages();
                    $send->sends($texts,$transaction->seller,Notification::TRANSACTION,$transaction->id);
                    $this->emitNotify('درخواست معامله با موفیت ارسال شد.');
                } else $this->addError('request','متاسفانه حساب کابری شما به دلیل نقض قوانین برای مدتی محدود شده است.مدتی بعد دوباره تلاش کنید');
            } else $this->addError('request','ین اگهی در دست رس نمی باشد');
        } else redirect()->route('auth');
        return;
    }

    public function addToFavorite()
    {
        Cart::add(\App\Cart\Cart::SAVED,$this->order);
    }

    public function unSave()
    {
        Cart::delete(\App\Cart\Cart::SAVED,$this->order->id);
    }

    public function render()
    {
        return view('livewire.site.orders.single-order');
    }

}

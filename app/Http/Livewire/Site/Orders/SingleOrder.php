<?php

namespace App\Http\Livewire\Site\Orders;

use App\Models\Save;
use App\Models\Setting;
use App\Models\Task;
use App\Models\OrderTransaction;
use App\Models\OrderTransactionData;
use App\Sends\SendMessages;
use App\Traits\Admin\Sends;
use App\Traits\Admin\TextBuilder;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;
use App\Models\Order;


class SingleOrder extends Component
{
    use TextBuilder;
    public $order , $data = [] , $saved = false;
    public function mount($userID, $id , $slug)
    {
        $this->order = Order::where([
            ['status',Order::IS_CONFIRMED],
            ['user_id',$userID],
        ])->findOrFail($id);

        \Cart::add(array(
            'id' => $id, // inique row ID
            'name' => 'last',
            'price' => 0,
            'quantity' => 1,
            'attributes' => array()
        ));

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
        if (Auth::check())
        {
            if ($this->order->status == Order::IS_CONFIRMED)
            {
                $ban = Carbon::make(now())->diff(\auth()->user()->ban)->format('%r%i');
                if ($ban <= 0) {
                    $transaction = new OrderTransaction();
                    $transaction->customer_id = Auth::id();
                    $transaction->seller_id = $this->order->user_id;
                    $transaction->order_id = $this->order->id;
                    $transaction->status = OrderTransaction::IS_CONFIRMED;
                    $transaction->timer = Carbon::make(now())->addHours(Setting::getSingleRow('confirmedTimer'));
                    $transaction->save();
                    $data = new OrderTransactionData();
                    $data->transaction_id = $transaction->id;
                    $data->name = $transaction->id;
                    $data->save();
                    $texts = $this->createText('request_order',$transaction);
                    $send = new SendMessages();
                    $send->sends($texts,$transaction->seller);
                    redirect()->route('user.store.transaction',['edit',$transaction->id]);
                } else $this->addError('request','متاسفانه حساب کابری شما به دلیل نقض قوانین برای مدتی محدود شده است.مدتی بعد دوباره تلاش کنید');
            } else $this->addError('request','ین اگهی در دست رس نمی باشد');
        } else redirect()->route('auth');
        return;
    }

    public function addToFavorite()
    {
        $id = $this->order->id;
        Save::create([
            'order_id' => $id,
            'user_id' => \auth()->id(),
        ]);
        $this->saved = true;
    }

    public function unSave()
    {
        Save::where('user_id',\auth()->id())->where('order_id',$this->order->id)->first()->delete();
        $this->saved = false;
    }

    public function render()
    {
        $save =  Save::where('user_id',\auth()->id())->where('order_id',$this->order->id)->first();
        $this->saved = $save ? true : false;
        return view('livewire.site.orders.single-order');
    }

}

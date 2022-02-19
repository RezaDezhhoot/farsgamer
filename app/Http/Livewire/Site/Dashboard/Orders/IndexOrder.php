<?php

namespace App\Http\Livewire\Site\Dashboard\Orders;

use App\Http\Livewire\BaseComponent;
use App\Models\Order;
use App\Models\Setting;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;

class IndexOrder extends BaseComponent
{
    public $status , $user , $data = [],$paginate = 10;
    protected $queryString = ['status'];

    public function mount()
    {
        $this->user = auth()->user();
        SEOMeta::setTitle('اگهی های من',false);
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle('اگهی های من');
        TwitterCard::setTitle('اگهی های من');
        JsonLd::setTitle('اگهی های من');
        JsonLd::addImage(Setting::getSingleRow('logo'));
        $this->data['status'] = Order::getStatus();
    }

    public function render()
    {
        $orders = $this->user->orders()->when($this->status,function ($query){
            return $query->where('status',$this->status);
        })->paginate($this->paginate);
        return view('livewire.site.dashboard.orders.index-order',['orders'=>$orders]);
    }

    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        if (in_array($order->status,[Order::IS_CONFIRMED,Order::IS_REJECTED,Order::IS_UNCONFIRMED,Order::IS_NEW]))
            $order->delete();
        $this->emitNotify('اگهی مورد نطر با موفقیت پاک شد');
    }
}

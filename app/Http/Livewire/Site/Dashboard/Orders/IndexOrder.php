<?php

namespace App\Http\Livewire\Site\Dashboard\Orders;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexOrder extends Component
{
    public $status , $user , $data = [],$paginate = 10;
    protected $queryString = ['status'];

    public function mount()
    {
        $this->user = User::findOrFail(Auth::id());
        SEOMeta::setTitle('اگهی های من',false);
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle('اگهی های من');
        TwitterCard::setTitle('اگهی های من');
        JsonLd::setTitle('اگهی های من');
        JsonLd::addImage(Setting::getSingleRow('logo'));
        $this->data['status'] = Order::getOrdersStatus();
    }

    public function render()
    {
        $orders = $this->user->orders()->when($this->status,function ($query){
            return $query->where('status',$this->status);
        })->paginate($this->paginate);
        return view('livewire.site.dashboard.orders.index-order');
    }
}

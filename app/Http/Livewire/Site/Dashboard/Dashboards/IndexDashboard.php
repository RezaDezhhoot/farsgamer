<?php

namespace App\Http\Livewire\Site\Dashboard\Dashboards;

use App\Http\Livewire\BaseComponent;
use App\Models\OrderTransaction;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Setting;
use App\Cart\Cart;
class IndexDashboard extends BaseComponent
{
    public $user , $orders , $results , $data = [];

    public function mount()
    {
        $this->user = \auth()->user();
        $this->data['comments'] = Comment::where([
            ['type',Comment::USER],
            ['status',Comment::CONFIRMED],
            ['case_id',$this->user->id],
        ])->get();
        $this->data['notifications'] = $this->user->alerts;
        $this->data['count'] = [
            'orders' => $this->user->orders()->count(),
            'views' => $this->user->orders()->sum('view_count'),
            'transactions' => OrderTransaction::where('customer_id',$this->user->id)->orWhere('seller_id',$this->user->id)->count()
        ];
        $this->data['saved'] = \Cart::content('saved');
        $this->data['last_view'] = \Cart::content('last_view');
    }
    public function render()
    {
        return view('livewire.site.dashboard.dashboards.index-dashboard');
    }
}

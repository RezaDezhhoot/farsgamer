<?php

namespace App\Http\Livewire\Site\Dashboard\Dashboards;

use App\Models\OrderTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;

class IndexDashboard extends Component
{
    public $user , $orders , $comments , $results , $data = [];

    public function mount()
    {
        $this->user = User::findOrFail(Auth::id());
        $this->comments = Comment::where([
            ['type',Comment::USER],
            ['status',Comment::CONFIRMED],
            ['case_id',$this->user->id],
        ])->get();

        $this->results = $this->user->results;
        $this->data['count'] = [
            'orders' => $this->user->orders()->count(),
            'views' => $this->user->orders()->sum('view_count'),
            'order-transactions' => $this->user->transactions()->where('status','!=',OrderTransaction::IS_COMPLETED)
                ->where('status','!=',OrderTransaction::IS_CANCELED)->count()
        ];
    }
    public function render()
    {
        return view('livewire.site.dashboard.dashboards.index-dashboard');
    }
}

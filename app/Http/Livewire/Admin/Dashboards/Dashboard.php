<?php

namespace App\Http\Livewire\Admin\Dashboards;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;
use App\Models\User , App\Models\Order , App\Models\OrderTransaction;
use App\Models\Payment , App\Models\Request;
use App\Models\Category , App\Models\Platform;
use App\Models\Ticket , App\Models\Comment;

class Dashboard extends Component
{
    public $from_date , $to_date , $now , $box;

    public function mount()
    {
        $this->now = Carbon::now()->format('Y-m-d');
        $this->to_date = Carbon::now()->format('Y-m-d');
        $this->from_date = Carbon::now()->subDays(5)->format('Y-m-d');
        $this->getData();
    }

    public function emitEvent()
    {
        $this->emit('runChart',$this->getChartData());
    }

    public function render()
    {
        $list = [
            'categories' => Category::withCount('orders')->orderBy('id', 'desc')->take(20)->get(),
            'platforms' => Platform::withCount('orders')->orderBy('id', 'desc')->take(20)->get(),
            'comments' => Comment::latest('id')->take(30)->get(),
            'tickets' => Ticket::latest('id')->where([
                ['sender_type',Ticket::USER],
            ])->take(30)->get(),
        ];
        return view('livewire.admin.dashboards.dashboard',['list'=>$list])
            ->extends('livewire.admin.layouts.admin');
    }
    public function confirmFilter()
    {
        $this->getData();
        $this->emit('runChart',$this->getChartData());
    }

    public function getData()
    {
        $this->box = [
            'orders'=> Order::whereBetween('created_at', [$this->from_date." 00:00:00", $this->to_date." 23:59:59"])->count(),
            'order-transactions'=> OrderTransaction::whereBetween('created_at', [$this->from_date." 00:00:00", $this->to_date." 23:59:59"])->count(),
            'users'=> User::whereBetween('created_at', [$this->from_date." 00:00:00", $this->to_date." 23:59:59"])->count(),
            'payments'=> Payment::whereBetween('created_at', [$this->from_date." 00:00:00", $this->to_date." 23:59:59"])->sum('amount'),
            'requests'=> Request::where('status',Request::NEW)->whereBetween('created_at', [$this->from_date." 00:00:00", $this->to_date." 23:59:59"])->sum('price'),
            'settlements'=> Request::where('status',Request::SETTLEMENT)->whereBetween('created_at', [$this->from_date." 00:00:00", $this->to_date." 23:59:59"])->sum('price'),
        ];
    }

    public function getChartData()
    {
        $chart = [];
        $dates = $this->getDates();
        $chartModels = [
            'payments' => ['model' => new Payment(),
                'where' => ['payment_ref' , '!=' , null] , 'sum' => 'amount'],
            'settlements' => ['model' => new Request(),
                'where' => ['status', Request::SETTLEMENT] , 'sum' => 'price'],
            'requests' => ['model' => new Request(),
                'where' => ['status', Request::NEW] , 'sum' => 'price'],
        ];
        foreach ($chartModels as $key => $chartModel) {
            $chart[$key] = [];
            $chart['label'] = [];
            for ($i = 0 ; $i< count($dates); $i++) {
                array_push($chart[$key],
                    (float)$chartModel['model']->where([$chartModel['where']])
                    ->whereBetween('created_at', [$dates[$i]->format('Y-m-d')." 00:00:00", $dates[$i]->format('Y-m-d')." 23:59:59"])
                    ->sum($chartModel['sum']));
                array_push($chart['label'] ,(string)$dates[$i]->format('Y-m-d') );

            }
        }
        return $chart;
    }
    public function getDates()
    {
        $period = CarbonPeriod::create($this->from_date, $this->to_date);
        foreach ($period as $date) {
            $date->format('Y-m-d');
        }
        return $period->toArray();
    }
}

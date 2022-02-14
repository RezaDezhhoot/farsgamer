<?php

namespace App\Http\Livewire\Admin\OrderTransactions;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use App\Models\OrderTransaction;
use Livewire\WithPagination;


class IndexOrderTransaction extends Component
{
    use WithPagination , AuthorizesRequests;

    protected $queryString = ['status','category','way'];
    public $status , $statusCount , $category , $placeholder = 'کد معامله';
    public $pagination = 5 , $search , $data = [] , $way = 0;

    public function render()
    {
        $this->authorize('show_transactions');
        $transactions = OrderTransaction::latest('id')->with(['order'])->when($this->status, function ($query) {
            return $query->where('status', $this->status)->where('is_returned',$this->way);
        })->when($this->search, function ($query) {
            return $query->where('id', $this->search);
        })->when($this->category,function ($query){
            return $query->whereHas('order',function ($query){
                return $query->where('category_id',$this->category);
            });
        })->when($this->way,function ($query) {
            return $query->where('is_returned',$this->way);
        })->paginate($this->pagination);

        $this->data['category'] = Category::all()->pluck('title','id');
        $this->data['status'] = OrderTransaction::getStatus($this->way);
        $this->data['way'] = ['0' => 'عادی','1' => 'مرجوعی'];
        $this->statusCount['all'] = OrderTransaction::count();
        $this->statusCount['requested'] = $this->getCount('requested');
        $this->statusCount['wait_for_confirm'] = $this->getCount('wait_for_confirm');
        $this->statusCount['wait_for_pay'] = $this->getCount('wait_for_pay');
        $this->statusCount['wait_for_send'] = $this->getCount('wait_for_send');
        $this->statusCount['wait_for_receive'] = $this->getCount('wait_for_receive');
        $this->statusCount['wait_for_no_receive'] = $this->getCount('wait_for_no_receive');
        $this->statusCount['wait_for_testing'] = $this->getCount('wait_for_testing');
        $this->statusCount['wait_for_complete'] = $this->getCount('wait_for_complete');
        $this->statusCount['wait_for_control'] = $this->getCount('wait_for_control');
        $this->statusCount['wait_for_sending_data'] = $this->getCount('wait_for_sending_data');
        $this->statusCount['returned'] = OrderTransaction::where('status',OrderTransaction::IS_RETURNED)->count();
        $this->statusCount['canceled'] = OrderTransaction::where('status',OrderTransaction::IS_CANCELED)->count();
        return view('livewire.admin.order-transactions.index-order-transaction' , ['transactions' => $transactions])
            ->extends('livewire.admin.layouts.admin');
    }

    public function getCount($status)
    {
        return OrderTransaction::where('status', $status)->where('is_returned',$this->way)->count();
    }
}

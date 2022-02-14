<?php

namespace App\Http\Livewire\Admin\Orders;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use App\Models\Order;
use App\Models\Category;
use App\Models\User;
use Livewire\WithPagination;

class IndexOrder extends Component
{
    use WithPagination , AuthorizesRequests;

    protected $queryString = ['status' ,'category'];
    public $status , $statusCount;
    public $pagination = 10 , $search , $data = [] , $category , $placeholder="شماره اگهی یا عنوان";

    public function render()
    {
        $this->authorize('show_orders');
        $orders = Order::latest('id')->when($this->status, function ($query) {
            return $query->where('status', $this->status);
        })->when($this->search, function ($query) {
            return is_numeric($this->search) ?
                $query->where('id', (int)$this->search) : $query->where('slug', $this->search);
        })->when($this->category,function ($query) {
            return $query->where('category_id',$this->category);
        })->paginate($this->pagination);

        $this->statusCount['all'] = Order::count();
        $this->statusCount['new'] = $this->getCount('new');
        $this->statusCount['unconfirmed'] = $this->getCount('unconfirmed');
        $this->statusCount['confirmed'] = $this->getCount('confirmed');
        $this->statusCount['rejected'] = $this->getCount('rejected');
        $this->statusCount['requested'] = $this->getCount('requested');
        $this->statusCount['finished'] = $this->getCount('finished');
        $this->data['category'] = Category::all()->pluck('title','id');

        return view('livewire.admin.orders.index-order' ,['orders' => $orders])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_orders');
        $order = Order::findOrFail($id);
        if (!in_array($order->status,[Order::IS_REQUESTED ,Order::IS_FINISHED]))
            $order->delete();
         else
            $this->emitNotify('برای این سفارش امکان حدف وجود ندارد','warning');
    }

    private function getCount($status)
    {
        return Order::where('status', $status)->count();
    }
}

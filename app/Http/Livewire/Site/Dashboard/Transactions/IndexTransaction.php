<?php

namespace App\Http\Livewire\Site\Dashboard\Transactions;

use App\Http\Livewire\BaseComponent;
use App\Models\OrderTransaction;
use Livewire\WithPagination;

class IndexTransaction extends BaseComponent
{
    use WithPagination;
    protected $queryString = ['status'];
    public $paginate = 15 , $status , $category;
    public function render()
    {
        $transactions = OrderTransaction::with(['order'])->when($this->status,function ($query) {
            return $this->status == 'seller' ? $query->where('seller_id',auth()->id()) :
                $query->where('customer_id',auth()->id());
        })->paginate($this->paginate);
        return view('livewire.site.dashboard.order-transactions.index-transaction',['order-transactions'=>$transactions]);
    }
}

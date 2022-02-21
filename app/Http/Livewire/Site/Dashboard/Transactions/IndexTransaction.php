<?php

namespace App\Http\Livewire\Site\Dashboard\Transactions;

use App\Http\Livewire\BaseComponent;
use App\Models\OrderTransaction;
use Livewire\WithPagination;

class IndexTransaction extends BaseComponent
{
    use WithPagination;
    protected $queryString = ['target'];
    public $paginate = 15 , $target , $category , $data = [];
    public function mount()
    {
        $this->data['status'] = ['seller','customer'];
    }
    public function render()
    {
        $transactions = OrderTransaction::with(['order'])->where(function ($query){
            return $query->where('seller_id',auth()->id())->orWhere('customer_id',auth()->id());
        })->when($this->target,function ($query) {
            return $this->target == 'seller' ? $query->where('seller_id',auth()->id()) :
                $query->where('customer_id',auth()->id());
        })->paginate($this->paginate);

        return view('livewire.site.dashboard.order-transactions.index-transaction',['transactions'=>$transactions]);
    }
}

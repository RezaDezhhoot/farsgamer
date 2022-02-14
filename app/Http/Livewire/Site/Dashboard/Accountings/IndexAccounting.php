<?php

namespace App\Http\Livewire\Site\Dashboard\Accountings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Request;

class IndexAccounting extends Component
{
    use WithPagination;
    protected $queryString = ['status'];
    public $status , $pagination = 10 , $data = [];

    public function render()
    {
        $requests = auth()->user()->requests()->latest('id')->with(['user'])->when($this->status, function ($query){
            return $query->where('status',$this->status);
        })->paginate($this->pagination);
        $this->data['status'] = Request::getStatus();
        $this->data['status'][Request::SETTLEMENT].= '('.Request::where('status',Request::SETTLEMENT)->count().')';
        $this->data['status'][Request::NEW].= '('.Request::where('status',Request::NEW)->count().')';
        return view('livewire.site.dashboard.accountings.index-accounting',['requests'=>$requests]);
    }

    public function payMore()
    {
        //
    }
}

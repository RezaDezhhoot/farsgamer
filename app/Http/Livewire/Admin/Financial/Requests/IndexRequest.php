<?php

namespace App\Http\Livewire\Admin\Financial\Requests;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Request;

class IndexRequest extends Component
{
    use WithPagination , AuthorizesRequests;
    protected $queryString = ['status'];
    public $status , $phone , $user_name;
    public $pagination = 10 , $search , $data = [] , $placeholder = 'کد پیگیری یا شماره درخواست';

    public function render()
    {
        $this->authorize('show_requests');
        $requests = Request::latest('id')->with(['user'])->when($this->status, function ($query){
            return $query->where('status',$this->status);
        })->when($this->phone,function ($query){
            return $query->whereHas('user',function ($query){
                return $query->where('phone',$this->phone);
            });
        })->when($this->user_name,function ($query){
            return $query->whereHas('user',function ($query){
                return $query->where('user_name',$this->user_name);
            });
        })->search($this->search)->paginate($this->pagination);

        $this->data['status'] = Request::getStatus();
        $this->data['status'][Request::SETTLEMENT].= '('.Request::where('status',Request::SETTLEMENT)->count().')';
        $this->data['status'][Request::REJECTED].= '('.Request::where('status',Request::REJECTED)->count().')';
        $this->data['status'][Request::NEW].= '('.Request::where('status',Request::NEW)->count().')';

        return view('livewire.admin.financial.requests.index-request',['requests'=>$requests])
            ->extends('livewire.admin.layouts.admin');
    }
}

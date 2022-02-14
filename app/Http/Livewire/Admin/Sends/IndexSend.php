<?php

namespace App\Http\Livewire\Admin\Sends;

use App\Models\Send;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class IndexSend extends Component
{
    use WithPagination , AuthorizesRequests;

    protected $queryString = ['status'];
    public $status;
    public $pagination = 10 , $search , $data = [],$placeholder = ' نام مستعار';

    public function render()
    {
        $this->authorize('show_sends');
        $sends = Send::latest('id')->when($this->status,function ($query){
            return $query->where('status',$this->status);
        })->search($this->search)->paginate($this->pagination);
        $this->data['status'] = Send::getStatus();
        return view('livewire.admin.sends.index-send',['sends'=>$sends])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_sends');
        Send::findOrFail($id)->delete();
    }
}

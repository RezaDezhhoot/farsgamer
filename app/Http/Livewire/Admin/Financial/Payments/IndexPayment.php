<?php

namespace App\Http\Livewire\Admin\Financial\Payments;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Payment;
use Livewire\WithPagination;

class IndexPayment extends BaseComponent
{
    use WithPagination , AuthorizesRequests;
    protected $queryString = ['status'];

    public $status , $ip , $user , $transaction;
    public $pagination = 5 , $search , $data = [] , $placeholder = '  کد پیگیری یا شماره رسید';

    public function render()
    {
        $this->authorize('show_payments');
        $payments = Payment::latest('id')->with(['user'])->when($this->ip,function ($query){
            return $query->wherehas('user',function ($query){
                return $query->where('ip',$this->ip);
            });
        })->when($this->user,function ($query){
            return $query->wherehas('user',function ($query){
                return
                    is_numeric($this->user) ?
                        $query->where('phone',$this->user) : $query->where('user_name',$this->user);
            });
        })->when($this->status,function ($query){
            return $query->where('status_code',$this->status);
        })->search($this->search)->paginate($this->pagination);
        $this->data['status'] = Payment::getStatus();

        return view('livewire.admin.financial.payments.index-payment',['payments'=>$payments])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_payments');
        Payment::findOrFail($id)->delete();
    }
}

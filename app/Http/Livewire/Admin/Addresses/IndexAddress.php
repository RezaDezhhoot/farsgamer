<?php

namespace App\Http\Livewire\Admin\Addresses;

use App\Models\Notification;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Livewire\BaseComponent;
use Livewire\WithPagination;
use App\Models\Address;

class IndexAddress extends BaseComponent
{
    use WithPagination , AuthorizesRequests , TextBuilder;

    protected $queryString = ['status'];
    public $status;
    public $pagination = 10 , $search , $data = [] , $number , $placeholder = 'شماره کاربر';

    public function render()
    {
        $this->authorize('show_addresses');
        $addresses = Address::with(['user'])->latest('id')->when($this->status,function ($query){
            return $query->where('status',$this->status);
        })->when($this->search,function ($query){
            return $query->whereHas('user',function ($query){
                return $query->where('phone',$this->search);
            });
        })->paginate($this->pagination);
        $this->data['status'] = Address::getStatus();
        return view('livewire.admin.addresses.index-address',['addresses'=>$addresses])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_addresses');
        Address::findOrFail($id)->delete();
    }

    public function confirm($id)
    {
        $this->authorize('edit_addresses');
        $address = Address::findOrFail($id);
        $address->status = Address::CONFIRMED;
        $address->save();
        $text = $this->createText('confirm_address',$address);
        $send = new SendMessages();
        $send->sends($text,$address->user,Notification::ADDRESS,$id);
    }
}

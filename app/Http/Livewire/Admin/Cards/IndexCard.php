<?php

namespace App\Http\Livewire\Admin\Cards;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Card;
use Livewire\WithPagination;

class IndexCard extends BaseComponent
{
    use WithPagination , AuthorizesRequests;
    protected $queryString = ['status'];
    public $status;
    public $pagination = 10 , $search , $data = [] , $placeholder = 'شماره کاربر';

    public function render()
    {
        $this->authorize('show_cards');
        $cards = Card::latest('id')->with(['user'])->when($this->search,function ($query){
            return $query->wherehas('user',function ($query){
                return $query->where('phone',$this->search);
            });
        })->when($this->status,function ($query){
            return $query->where('status',$this->status);
        })->paginate($this->pagination);

        $this->data['status'] = Card::getStatus();
        $this->data['status'][Card::CONFIRMED].= '('.Card::where('status',Card::CONFIRMED)->count().')';
        $this->data['status'][Card::NOT_CONFIRMED].= '('.Card::where('status',Card::NOT_CONFIRMED)->count().')';
        return view('livewire.admin.cards.index-card',['cards'=>$cards])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_cards');
        Card::findOrFail($id)->delete();
    }

}

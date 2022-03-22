<?php

namespace App\Http\Livewire\Admin\Cards;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CardRepositoryInterface;
use Livewire\WithPagination;

class IndexCard extends BaseComponent
{
    use WithPagination;
    protected $queryString = ['status'];
    public $status , $data = [] , $placeholder = 'شماره کاربر';

    public function render(CardRepositoryInterface $cardRepository)
    {
        $this->authorizing('show_cards');
        $cards = $cardRepository->getAllAdminList($this->search,$this->status,$this->pagination , false);
        $this->data['status'] = $cardRepository->getStatus();

        foreach ($this->data['status'] as $key => $value)
            $this->data['status'][$key] = $value.'('.$cardRepository->getByConditionCount('status','=',$key,false).')';


        return view('livewire.admin.cards.index-card',['cards'=>$cards])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , CardRepositoryInterface $cardRepository)
    {
        $this->authorizing('delete_cards');
        $card = $cardRepository->find($id,false);
        $cardRepository->delete($card);
    }

}

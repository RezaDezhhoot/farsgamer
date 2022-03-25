<?php

namespace App\Http\Livewire\Admin\OrderTransactions;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use Livewire\WithPagination;


class IndexOrderTransaction extends BaseComponent
{
    use WithPagination ;

    protected $queryString = ['status','category','way'];
    public $status , $statusCount , $category , $placeholder = 'کد معامله';
    public $data = [] , $way = 0;

    public function render(OrderTransactionRepositoryInterface $orderTransactionRepository , CategoryRepositoryInterface $categoryRepository)
    {
        $this->authorizing('show_transactions');
        $transactions = $orderTransactionRepository->getAllAdminList($this->status,$this->way,$this->search,$this->category,$this->pagination);

        $this->data['category'] = $categoryRepository->getAll(true,true)->pluck('title','id');
        $this->data['status'] = $orderTransactionRepository::getStatus($this->way);
        $this->data['way'] = ['0' => 'عادی','1' => 'مرجوعی'];
        $this->statusCount['all'] = $orderTransactionRepository::count();
        foreach ($this->data['status'] as $key => $item)
            $this->statusCount[$key] = $orderTransactionRepository->getCount($key,$this->way);

        return view('livewire.admin.order-transactions.index-order-transaction' , ['transactions' => $transactions])
            ->extends('livewire.admin.layouts.admin');
    }

}

<?php

namespace App\Http\Livewire\Admin\Orders;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Livewire\WithPagination;

class IndexOrder extends BaseComponent
{
    use WithPagination ;

    protected $queryString = ['status' ,'category'];
    public $status , $statusCount;
    public $data = [] , $category , $placeholder="شماره اگهی یا عنوان";

    public function render(OrderRepositoryInterface $orderRepository , CategoryRepositoryInterface $categoryRepository)
    {
        $this->authorizing('show_orders');

        $orders = $orderRepository->getAllAdminList($this->status,$this->search,$this->category,$this->pagination);

        $this->statusCount['all'] = $orderRepository->count();

        foreach ($orderRepository::getStatus() as $key => $value)
            $this->statusCount[$key] = $value.'('.$orderRepository->getCountWhere($key).')';

        $this->data['category'] = $categoryRepository->getAll(false)->pluck('title','id');

        return view('livewire.admin.orders.index-order' ,['orders' => $orders])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , OrderRepositoryInterface $orderRepository)
    {
        $this->authorizing('delete_orders');
        $order = $orderRepository->getOrder($id,false);
        if (!in_array($order->status,[$orderRepository::isRequestedStatus() ,$orderRepository::isFinishedStatus()]))
            $orderRepository->delete($order);
         else
            $this->emitNotify('برای این سفارش امکان حدف وجود ندارد','warning');
    }
}

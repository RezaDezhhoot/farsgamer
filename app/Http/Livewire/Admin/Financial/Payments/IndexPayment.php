<?php

namespace App\Http\Livewire\Admin\Financial\Payments;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Livewire\WithPagination;

class IndexPayment extends BaseComponent
{
    use WithPagination;
    protected $queryString = ['status'];

    public $status , $ip , $user , $transaction;
    public $data = [] , $placeholder = '  کد پیگیری یا شماره رسید';

    public function render(PaymentRepositoryInterface $paymentRepository)
    {
        $this->authorizing('show_payments');
        $payments = $paymentRepository->getAllAdminList($this->ip,$this->user,$this->status,$this->search,$this->pagination);
        $this->data['status'] = $paymentRepository::getStatus();

        return view('livewire.admin.financial.payments.index-payment',['payments'=>$payments])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , PaymentRepositoryInterface $paymentRepository)
    {
        $this->authorizing('delete_payments');
        $payment = $paymentRepository->find($id);
        $paymentRepository->delete($payment);
    }
}

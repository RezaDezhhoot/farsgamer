<?php

namespace App\Http\Livewire\Admin\Financial\Payments;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class StorePayment extends BaseComponent
{
    public $payment , $header , $mode , $data = [] , $json;

    public function mount(PaymentRepositoryInterface $paymentRepository , SettingRepositoryInterface $settingRepository,$action , $id = null)
    {
        $this->authorizing('show_payments');
        if ($action == 'edit')
        {
            $this->payment = $paymentRepository->find($id);
            $this->header = 'رسید پرداخت شماره '.$id;
        } else abort(404);
        $this->json = json_decode($this->payment->json,true);
        $this->data['status'] = $paymentRepository::getStatus();
        $this->data['province'] = $settingRepository::getProvince();
        $this->data['city'] = $settingRepository->getCity($this->payment->user->province);
        $this->mode = $action;
    }

    public function render()
    {
        return view('livewire.admin.financial.payments.store-payment')
            ->extends('livewire.admin.layouts.admin');
    }

    public function deleteItem(PaymentRepositoryInterface $paymentRepository)
    {
        $this->authorizing('delete_payments');
        $paymentRepository->delete($this->payment);
        return redirect()->route('admin.payment');
    }
}

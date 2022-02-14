<?php

namespace App\Http\Livewire\Admin\Financial\Payments;

use App\Http\Livewire\BaseComponent;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StorePayment extends BaseComponent
{
    use AuthorizesRequests;
    public $payment , $header , $mode , $data = [] , $json;

    public function mount($action , $id = null)
    {
        $this->authorize('show_payments');
        if ($action == 'edit')
        {
            $this->payment = Payment::findOrFail($id);
            $this->header = 'رسید پرداخت شماره '.$id;
        } else abort(404);
        $this->json = json_decode($this->payment->json,true);
        $this->data['status'] = Payment::getStatus();
        $this->data['province'] = Setting::getProvince();
        $this->data['city'] = Setting::getCity()[$this->payment->user->province];
        $this->mode = $action;
    }

    public function render()
    {
        return view('livewire.admin.financial.payments.store-payment')->extends('livewire.admin.layouts.admin');
    }

    public function deleteItem()
    {
        $this->authorize('delete_payments');
        $this->payment->delete();
        return redirect()->route('admin.payment');
    }
}

<?php

namespace App\Http\Livewire\Admin\Financial\Requests;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use Livewire\WithPagination;

class IndexRequest extends BaseComponent
{
    use WithPagination;
    protected $queryString = ['status'];
    public $status , $phone , $user_name;
    public $data = [] , $placeholder = 'کد پیگیری یا شماره درخواست';

    public function render(RequestRepositoryInterface $requestRepository)
    {
        $this->authorizing('show_requests');
        $requests = $requestRepository->getAllAdminList($this->search,$this->search,$this->phone,$this->user_name,$this->pagination);

        $this->data['status'] = $requestRepository::getStatus();
        foreach ($this->data['status'] as $key => $value)
            $this->data['status'][$key] = $value.'('.$requestRepository->getByConditionCount('status','=',$key).')';


        return view('livewire.admin.financial.requests.index-request',['requests'=>$requests])
            ->extends('livewire.admin.layouts.admin');
    }
}

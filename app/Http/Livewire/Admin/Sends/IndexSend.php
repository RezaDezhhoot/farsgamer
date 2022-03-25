<?php

namespace App\Http\Livewire\Admin\Sends;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SendRepositoryInterface;
use Livewire\WithPagination;

class IndexSend extends BaseComponent
{
    use WithPagination;

    protected $queryString = ['status'];
    public $status;
    public $data = [],$placeholder = ' نام مستعار';

    public function render(SendRepositoryInterface $sendRepository)
    {
        $this->authorizing('show_sends');
        $sends = $sendRepository->getAllAdminList($this->status,$this->search,$this->pagination);
        $this->data['status'] = $sendRepository->getStatus();
        return view('livewire.admin.sends.index-send',['sends'=>$sends])->extends('livewire.admin.layouts.admin');
    }

    public function delete(SendRepositoryInterface $sendRepository,$id)
    {
        $this->authorizing('delete_sends');
        $send = $sendRepository->find($id);
        $sendRepository->delete($send);
    }
}

<?php

namespace App\Http\Livewire\Admin\Offends;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\OffendRepositoryInterface;
use Livewire\WithPagination;

class IndexOffend extends BaseComponent
{
    use WithPagination;
    public $placeholder = 'شماره همراه یا نام کاربری';
    public function render(OffendRepositoryInterface $offendRepository)
    {
        $this->authorizing('show_offends');
        $offends = $offendRepository->getAllAdminList($this->search,$this->pagination);
        return view('livewire.admin.offends.index-offend',['offends'=>$offends])
            ->extends('livewire.admin.layouts.admin');
    }
}

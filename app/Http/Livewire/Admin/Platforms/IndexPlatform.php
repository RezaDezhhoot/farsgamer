<?php

namespace App\Http\Livewire\Admin\Platforms;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\PlatformRepositoryInterface;
use Livewire\WithPagination;

class IndexPlatform extends BaseComponent
{
    use WithPagination ;
    public $placeholder = ' نام مستعار';

    public function render(PlatformRepositoryInterface $platformRepository)
    {
        $this->authorizing('show_platforms');
        $platforms = $platformRepository->getAllAdminList($this->search,$this->pagination);
        return view('livewire.admin.platforms.index-platform',['platforms'=>$platforms])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , PlatformRepositoryInterface $platformRepository)
    {
        $this->authorizing('delete_platforms');
        $platform = $platformRepository->find($id);
        $platformRepository->delete($platform);
    }
}

<?php

namespace App\Http\Livewire\Admin\Platforms;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Platform;
use Livewire\WithPagination;

class IndexPlatform extends BaseComponent
{
    use WithPagination , AuthorizesRequests;
    public $pagination = 10 , $search ,$placeholder = ' نام مستعار';

    public function render()
    {
        $this->authorize('show_platforms');
        $platforms = Platform::latest('id')->search($this->search)->paginate($this->pagination);
        return view('livewire.admin.platforms.index-platform',['platforms'=>$platforms])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_platforms');
        Platform::findOrFail($id)->delete();
    }
}

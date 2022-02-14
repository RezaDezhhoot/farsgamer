<?php

namespace App\Http\Livewire\Admin\Layouts;

use App\Http\Livewire\BaseComponent;
use App\Traits\Admin\ChatList;

class Header extends BaseComponent
{
    use ChatList ;
    public $saveMessage;
    public function mount()
    {
    }
    public function render()
    {
        return view('livewire.admin.layouts.header');
    }
}

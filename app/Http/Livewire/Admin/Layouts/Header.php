<?php

namespace App\Http\Livewire\Admin\Layouts;

use App\Traits\Admin\ChatList;
use Livewire\Component;

class Header extends Component
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

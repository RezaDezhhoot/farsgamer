<?php

namespace App\Http\Livewire\Admin\Offends;

use Livewire\Component;

class IndexOffend extends Component
{
    public function render()
    {
        return view('livewire.admin.offends.index-offend')->extends('livewire.admin.layouts.admin');
    }
}

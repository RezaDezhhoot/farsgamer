<?php

namespace App\Http\Livewire\Admin\Offends;

use App\Http\Livewire\BaseComponent;

class IndexOffend extends BaseComponent
{
    public function render()
    {
        return view('livewire.admin.offends.index-offend')->extends('livewire.admin.layouts.admin');
    }
}

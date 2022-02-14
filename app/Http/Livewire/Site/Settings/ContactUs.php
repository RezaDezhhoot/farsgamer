<?php

namespace App\Http\Livewire\Site\Settings;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;

class ContactUs extends BaseComponent
{
    public $laws = [] , $header;

    public function mount()
    {
        $this->laws = Setting::where('name','law')->get() ?? [];
    }

    public function store()
    {

    }

    public function render()
    {
        return view('livewire.site.settings.contact-us')
            ->extends('livewire.admin.layouts.admin');
    }
}

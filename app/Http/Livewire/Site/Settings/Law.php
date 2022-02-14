<?php

namespace App\Http\Livewire\Site\Settings;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;

class Law extends BaseComponent
{
    public $laws = [] , $header;

    public function mount()
    {
        $this->header = 'تنظیمات قوانین';
        $this->laws = Setting::where('name','law')->get() ?? [];
    }

    public function store()
    {

    }

    public function render()
    {
        return view('livewire.site.settings.law')
            ->extends('livewire.admin.layouts.admin');
    }
}

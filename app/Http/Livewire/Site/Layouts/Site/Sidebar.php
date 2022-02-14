<?php

namespace App\Http\Livewire\Site\Layouts\Site;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;

class Sidebar extends BaseComponent
{
    public $data = [];
    public function render()
    {
        $this->data['contact'] = Setting::getSingleRow('contact',[]);

        return view('livewire.site.layouts.site.sidebar');
    }
}

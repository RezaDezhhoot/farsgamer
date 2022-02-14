<?php

namespace App\Http\Livewire\Site\Layouts\Site;

use App\Models\Setting;
use Livewire\Component;

class Sidebar extends Component
{
    public $data = [];
    public function render()
    {
        $this->data['contact'] = Setting::getSingleRow('contact',[]);

        return view('livewire.site.layouts.site.sidebar');
    }
}

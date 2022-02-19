<?php

namespace App\Http\Livewire\Site\Layouts\Site;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;

class Footer extends BaseComponent
{
    public $data = [] , $email;
    public function mount()
    {
        $this->data = [
            'copyRight' => Setting::getSingleRow('copyRight'),
            'tel' => Setting::getSingleRow('tel'),
            'logo' => Setting::getSingleRow('logo'),
            'email' => Setting::getSingleRow('email'),
            'address' => Setting::getSingleRow('address'),
            'contact' => Setting::getSingleRow('contact'),
        ];
    }
    public function render()
    {
        return view('livewire.site.layouts.site.footer');
    }
}

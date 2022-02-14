<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Setting;

class HomeSetting extends BaseComponent
{
    use AuthorizesRequests;
    public $header , $contact = [];
    public function mount()
    {
        $this->authorize('show_settings_home');
        $this->header = 'تنظیمات صفحه اصلی';
    }
    public function render()
    {
        return view('livewire.admin.settings.home-setting')
            ->extends('livewire.admin.layouts.admin');
    }

    public function store()
    {
        $this->authorize('show_settings_home');
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

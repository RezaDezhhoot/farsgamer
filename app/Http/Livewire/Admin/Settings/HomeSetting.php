<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class HomeSetting extends BaseComponent
{
    public $header , $contact = [];
    public function mount(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('show_settings_home');
        $this->header = 'تنظیمات صفحه اصلی';
    }
    public function render()
    {
        return view('livewire.admin.settings.home-setting')
            ->extends('livewire.admin.layouts.admin');
    }

    public function store(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('edit_settings_home');
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

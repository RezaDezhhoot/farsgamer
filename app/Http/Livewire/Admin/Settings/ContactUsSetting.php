<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class ContactUsSetting extends BaseComponent
{
    public $header , $googleMap  , $contactText;

    public function mount(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('show_settings_contactUs');
        $this->header = 'تنظیمات ارتباط با ما';
        $this->googleMap = $settingRepository->getSiteFaq('googleMap');
        $this->contactText = $settingRepository->getSiteFaq('contactText');
    }

    public function render()
    {
        return view('livewire.admin.settings.contact-us-setting')
            ->extends('livewire.admin.layouts.admin');
    }

    public function store(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('edit_settings_contactUs');
        $this->validate(
            [
                'googleMap' => ['nullable', 'string','max:10000'],
                'contactText' => ['nullable','string','max:10000'],
            ] , [] , [
                'googleMap' => 'شناسه گوگل مپ',
                'contactText' => 'متن',
            ]
        );
        $settingRepository::updateOrCreate(['name' => 'googleMap'],['value' => $this->googleMap]);
        $settingRepository::updateOrCreate(['name' => 'contactText'],['value' => $this->contactText]);

        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

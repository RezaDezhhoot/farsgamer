<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class AboutUsSetting extends BaseComponent
{
    public $aboutUsImages , $aboutUs , $header;

    public function mount(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('show_settings_aboutUs');
        $this->header = 'تنظیمات درباره ما';
        $this->aboutUsImages = $settingRepository->getSiteFaq('aboutUsImages');
        $this->aboutUs = $settingRepository->getSiteFaq('aboutUs');
    }

    public function store(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('edit_settings_aboutUs');
        $this->validate(
            [
                'aboutUs' => ['nullable', 'string','max:600000'],
                'aboutUsImages' => ['nullable','string','max:600000'],
            ] , [] , [
                'aboutUs' => 'درباره ما',
                'aboutUsImages' => 'اسلایدر درباره ما',
            ]
        );
        $settingRepository::updateOrCreate(['name' => 'aboutUsImages'], ['value' => $this->aboutUsImages]);
        $settingRepository::updateOrCreate(['name' => 'aboutUs'], ['value' => $this->aboutUs]);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function render()
    {
        return view('livewire.admin.settings.about-us-setting')
            ->extends('livewire.admin.layouts.admin');
    }
}

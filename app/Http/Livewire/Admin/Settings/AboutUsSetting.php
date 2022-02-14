<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Setting;

class AboutUsSetting extends BaseComponent
{
    use AuthorizesRequests;
    public $aboutUsImages , $aboutUs , $header;

    public function mount()
    {
        $this->authorize('show_settings_aboutUs');
        $this->header = 'تنظیمات درباره ما';
        $this->aboutUsImages = Setting::getSingleRow('aboutUsImages');
        $this->aboutUs = Setting::getSingleRow('aboutUs');
    }

    public function store()
    {
        $this->authorize('edit_settings_aboutUs');
        $this->validate(
            [
                'aboutUs' => ['nullable', 'string','max:600000'],
                'aboutUsImages' => ['nullable','string'],
            ] , [] , [
                'aboutUs' => 'درباره ما',
                'aboutUsImages' => 'اسلایدر درباره ما',
            ]
        );
        Setting::updateOrCreate(['name' => 'aboutUsImages'], ['value' => $this->aboutUsImages]);
        Setting::updateOrCreate(['name' => 'aboutUs'], ['value' => $this->aboutUs]);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function render()
    {
        return view('livewire.admin.settings.about-us-setting')
            ->extends('livewire.admin.layouts.admin');
    }
}

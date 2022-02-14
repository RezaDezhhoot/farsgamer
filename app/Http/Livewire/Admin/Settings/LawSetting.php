<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LawSetting extends BaseComponent
{
    use AuthorizesRequests;
    public $laws = [] , $header;
    public function delete($id)
    {
        $this->authorize('edit_settings_law');
        Setting::findOrFail($id)->delete();
    }

    public function render()
    {
        $this->authorize('show_settings_law');
        $this->header = 'تنظیمات قوانین';
        $this->laws = Setting::where('name','law')->get()->toArray() ?? [];
        return view('livewire.admin.settings.law-setting')
            ->extends('livewire.admin.layouts.admin');
    }

}

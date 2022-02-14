<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChatLawSetting extends BaseComponent
{
    use AuthorizesRequests;
    public $laws = [] , $header;
    public function delete($id)
    {
        $this->authorize('edit_settings_chatLaw');
        Setting::findOrFail($id)->delete();
    }
    public function render()
    {
        $this->authorize('show_settings_chatLaw');
        $this->header = ' تنظیمات قوانین چت';
        $this->laws = Setting::where('name','chatLaw')->get()->toArray() ?? [];
        return view('livewire.admin.settings.chat-law-setting')
            ->extends('livewire.admin.layouts.admin');
    }
}

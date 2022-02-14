<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class QuestionSetting extends Component
{
    use AuthorizesRequests;
    public $questions = [] , $header;
    public function delete($id)
    {
        $this->authorize('edit_settings_fag');
        Setting::findOrFail($id)->delete();
    }
    public function render()
    {
        $this->authorize('show_settings_fag');
        $this->header = 'تنظیمات سوالات متداول';
        $this->questions = Setting::where('name','question')->get()->toArray() ?? [];
        return view('livewire.admin.settings.question-setting')
            ->extends('livewire.admin.layouts.admin');
    }
}

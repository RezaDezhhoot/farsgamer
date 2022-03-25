<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class QuestionSetting extends BaseComponent
{
    public $header;
    public function delete(SettingRepositoryInterface $settingRepository,$id)
    {
        $this->authorizing('edit_settings_fag');
        $settings = $settingRepository->find($id);
        $settingRepository->delete($settings);
    }
    public function render(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('show_settings_fag');
        $this->header = 'تنظیمات سوالات متداول';
        $questions = $settingRepository->getAdminLaw('question');
        return view('livewire.admin.settings.question-setting',['questions' => $questions])
            ->extends('livewire.admin.layouts.admin');
    }
}

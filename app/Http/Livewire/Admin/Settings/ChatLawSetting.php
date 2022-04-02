<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;


class ChatLawSetting extends BaseComponent
{
    public  $header;
    public function delete(SettingRepositoryInterface $settingRepository,$id)
    {
        $this->authorizing('edit_settings_chatLaw');
        $settings = $settingRepository->find($id);
        $settingRepository->delete($settings);
    }

    public function render(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('show_settings_chatLaw');
        $this->header = ' تنظیمات قوانین چت';
        $laws = $settingRepository->getAdminLaw('chatLaw');
        return view('livewire.admin.settings.chat-law-setting',['laws'=>$laws])
            ->extends('livewire.admin.layouts.admin');
    }
}

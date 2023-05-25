<?php

namespace App\Http\Livewire\Admin\PhoneLaw;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class IndexPHoneLaw extends BaseComponent
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
        $this->header = ' تنظیمات قوانین شماره';
        $laws = $settingRepository->getAdminLaw('phoneLaw');
        return view('livewire.admin.phone-law.index-p-hone-law',get_defined_vars())->extends('livewire.admin.layouts.admin');
    }
}

<?php

namespace App\Http\Livewire\Admin\TransactionLaw;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class IndexTransactionLaw extends BaseComponent
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
        $this->header = ' تنظیمات قوانین قبل معامله';
        $laws = $settingRepository->getAdminLaw('transactionLaw');
        return view('livewire.admin.transaction-law.index-transaction-law',get_defined_vars())->extends('livewire.admin.layouts.admin');
    }
}

<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;


class BaseSetting extends BaseComponent
{
    public $header , $name , $logo , $status , $title  , $copyRight , $subject = [],$offends = [] ,$logInImage , $contact = [] , $waterMark;
    public  $data = [] , $i = 1 , $registerGift , $notification  , $tel , $email, $address, $seoDescription , $seoKeyword ,$categoryHomeCount;

    public function mount(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('show_settings_base');
        $this->header = 'تنظیمات پایه';
        $this->data['status'] = ['0' => 'بسته','1' => 'باز'];
        $this->contact = $settingRepository->getSiteFaq('contact',[]);
        $this->subject = $settingRepository->getSiteFaq('subject',[]);
        $this->offends = $settingRepository->getSiteFaq('offends',[]);
        $this->copyRight = $settingRepository->getSiteFaq('copyRight');
        $this->status = $settingRepository->getSiteFaq('status');
        $this->logo = $settingRepository->getSiteFaq('logo');
        $this->waterMark = $settingRepository->getSiteFaq('waterMark');
        $this->title = $settingRepository->getSiteFaq('title');
        $this->name = $settingRepository->getSiteFaq('name');
        $this->registerGift = $settingRepository->getSiteFaq('registerGift');
        $this->notification = $settingRepository->getSiteFaq('notification');
        $this->tel = $settingRepository->getSiteFaq('tel');
        $this->email = $settingRepository->getSiteFaq('email');
        $this->address = $settingRepository->getSiteFaq('address');
        $this->seoDescription = $settingRepository->getSiteFaq('seoDescription');
        $this->seoKeyword = $settingRepository->getSiteFaq('seoKeyword');
        $this->logInImage = $settingRepository->getSiteFaq('logInImage');
        $this->categoryHomeCount = $settingRepository->getSiteFaq('categoryHomeCount');
    }

    public function render()
    {
        return view('livewire.admin.settings.base-setting')
            ->extends('livewire.admin.layouts.admin');
    }

    public function addSubject()
    {
        $this->i = $this->i+ 1;
        array_push($this->subject,'');
    }

    public function addOffend()
    {
        $this->i = $this->i+ 1;
        array_push($this->offends,'');
    }

    public function store(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('edit_settings_base');
        $this->validate(
            [
                'name' => ['required', 'string','max:120'],
                'title' => ['required','string','max:120'],
                'logo' => ['required','required','max:300'],
                'waterMark' => ['required','required','max:300'],
                'status' => ['required','string','in:1,0'],
                'registerGift' => ['nullable','numeric','between:0,99999999999.999999'],
                'notification' => ['nullable','string','max:300'],
                'tel' => ['required','string','max:40'],
                'address' => ['required','string','max:250'],
                'email' => ['required','email','max:150'],
                'subject' => ['nullable','array'],
                'subject.*' => ['required','string','max:70'],
                'offends' => ['nullable','array'],
                'offends.*' => ['required','string','max:70'],
                'seoDescription' => ['required','string','max:400'],
                'seoKeyword' => ['required','string','max:400'],
                'logInImage' => ['required','string','max:300'],
                'contact' => ['nullable','array'],
                'contact.*.img' => ['required','string',"max:70"],
                'contact.*.link' => ['required','url',"max:400"],
                'categoryHomeCount' => ['nullable','integer','between:1,10000'],
            ] , [] , [
                'name' => 'نام سایت',
                'title' => 'عنوان سایت',
                'logo' => 'لوکو سایت',
                'waterMark' => 'تصویر واتر مارک',
                'status' => 'وضعیت سایت',
                'registerGift' => 'هدیه ثبت نام',
                'notification' => 'اعلان بالا صفحه',
                'tel' => 'تلفن',
                'address' => 'ادرس',
                'email' => 'ایمیل',
                'subject' => 'موضوع ها',
                'subject.*' => 'موضوع ها',
                'offends' => 'موضوعات تخلف',
                'offends.*' => 'موضوعات تخلف',
                'seoDescription' => 'توضیحات سئو',
                'seoKeyword' => 'کلمات سئو',
                'logInImage' => 'تصویر صفحه ورود',
                'contact' => 'لینک های ارتباطی',
                'categoryHomeCount' => 'تعداد دسته بندی های قابل نمایش صفحه اصلی',
            ]
        );
        $settingRepository::updateOrCreate(['name' => 'subject'], ['value' => json_encode($this->subject)]);
        $settingRepository::updateOrCreate(['name' => 'copyRight'], ['value' => $this->copyRight]);
        $settingRepository::updateOrCreate(['name' => 'status'], ['value' => $this->status]);
        $settingRepository::updateOrCreate(['name' => 'logo'], ['value' => $this->logo]);
        $settingRepository::updateOrCreate(['name' => 'waterMark'], ['value' => $this->waterMark]);
        $settingRepository::updateOrCreate(['name' => 'title'], ['value' => $this->title]);
        $settingRepository::updateOrCreate(['name' => 'name'], ['value' => $this->name]);
        $settingRepository::updateOrCreate(['name' => 'notification'], ['value' => $this->notification]);
        $settingRepository::updateOrCreate(['name' => 'tel'], ['value' => $this->tel]);
        $settingRepository::updateOrCreate(['name' => 'email'], ['value' => $this->email]);
        $settingRepository::updateOrCreate(['name' => 'address'], ['value' => $this->address]);
        $settingRepository::updateOrCreate(['name' => 'seoDescription'], ['value' => $this->seoDescription]);
        $settingRepository::updateOrCreate(['name' => 'seoKeyword'], ['value' => $this->seoKeyword]);
        $settingRepository::updateOrCreate(['name' => 'logInImage'], ['value' => $this->logInImage]);
        $settingRepository::updateOrCreate(['name' => 'registerGift'], ['value' => $this->registerGift]);
        $settingRepository::updateOrCreate(['name' => 'contact'], ['value' => json_encode($this->contact)]);
        $settingRepository::updateOrCreate(['name' => 'categoryHomeCount'], ['value' => $this->categoryHomeCount]);
        $settingRepository::updateOrCreate(['name' => 'offends'], ['value' => json_encode($this->offends)]);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteSubject($key)
    {
        unset($this->subject[$key]);
    }

    public function deleteOffend($key)
    {
        unset($this->offends[$key]);
    }
    public function addLink()
    {
        $this->i = $this->i+ 1;
        array_push($this->contact,'');
    }

    public function delete($key)
    {
        unset($this->contact[$key]);
    }
}

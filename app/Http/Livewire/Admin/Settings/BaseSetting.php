<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseSetting extends BaseComponent
{
    use AuthorizesRequests;
    public $header , $name , $logo , $status , $title  , $copyRight , $subject = [],$offends = [] ,$logInImage , $contact = [] , $waterMark;
    public  $data = [] , $i = 1 , $registerGift , $notification  , $tel , $email, $address, $seoDescription , $seoKeyword ,$categoryHomeCount;

    public function mount()
    {
        $this->authorize('show_settings_base');
        $this->header = 'تنظیمات پایه';
        $this->data['status'] = ['0' => 'بسته','1' => 'باز'];
        $this->contact = Setting::getSingleRow('contact',[]);
        $this->subject = Setting::getSingleRow('subject',[]);
        $this->offends = Setting::getSingleRow('offends',[]);
        $this->copyRight = Setting::getSingleRow('copyRight');
        $this->status = Setting::getSingleRow('status');
        $this->logo = Setting::getSingleRow('logo');
        $this->waterMark = Setting::getSingleRow('waterMark');
        $this->title = Setting::getSingleRow('title');
        $this->name = Setting::getSingleRow('name');
        $this->registerGift = Setting::getSingleRow('registerGift');
        $this->notification = Setting::getSingleRow('notification');
        $this->tel = Setting::getSingleRow('tel');
        $this->email = Setting::getSingleRow('email');
        $this->address = Setting::getSingleRow('address');
        $this->seoDescription = Setting::getSingleRow('seoDescription');
        $this->seoKeyword = Setting::getSingleRow('seoKeyword');
        $this->logInImage = Setting::getSingleRow('logInImage');
        $this->categoryHomeCount = Setting::getSingleRow('categoryHomeCount');
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

    public function store()
    {
        $this->authorize('show_settings_base');
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
        Setting::updateOrCreate(['name' => 'subject'], ['value' => json_encode($this->subject)]);
        Setting::updateOrCreate(['name' => 'copyRight'], ['value' => $this->copyRight]);
        Setting::updateOrCreate(['name' => 'status'], ['value' => $this->status]);
        Setting::updateOrCreate(['name' => 'logo'], ['value' => $this->logo]);
        Setting::updateOrCreate(['name' => 'waterMark'], ['value' => $this->waterMark]);
        Setting::updateOrCreate(['name' => 'title'], ['value' => $this->title]);
        Setting::updateOrCreate(['name' => 'name'], ['value' => $this->name]);
        Setting::updateOrCreate(['name' => 'notification'], ['value' => $this->notification]);
        Setting::updateOrCreate(['name' => 'tel'], ['value' => $this->tel]);
        Setting::updateOrCreate(['name' => 'email'], ['value' => $this->email]);
        Setting::updateOrCreate(['name' => 'address'], ['value' => $this->address]);
        Setting::updateOrCreate(['name' => 'seoDescription'], ['value' => $this->seoDescription]);
        Setting::updateOrCreate(['name' => 'seoKeyword'], ['value' => $this->seoKeyword]);
        Setting::updateOrCreate(['name' => 'logInImage'], ['value' => $this->logInImage]);
        Setting::updateOrCreate(['name' => 'registerGift'], ['value' => $this->registerGift]);
        Setting::updateOrCreate(['name' => 'contact'], ['value' => json_encode($this->contact)]);
        Setting::updateOrCreate(['name' => 'categoryHomeCount'], ['value' => $this->categoryHomeCount]);
        Setting::updateOrCreate(['name' => 'offends'], ['value' => json_encode($this->offends)]);
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

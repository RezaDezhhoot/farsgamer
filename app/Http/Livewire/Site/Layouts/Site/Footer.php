<?php

namespace App\Http\Livewire\Site\Layouts\Site;

use App\Models\Email;
use Livewire\Component;
use App\Models\Setting;

class Footer extends Component
{
    public $data = [] , $email;
    public function render()
    {
        $this->data = [
            'copyRight' => Setting::getSingleRow('copyRight'),
            'tel' => Setting::getSingleRow('tel'),
            'logo' => Setting::getSingleRow('logo'),
            'email' => Setting::getSingleRow('email'),
            'address' => Setting::getSingleRow('address'),
            'contact' => Setting::getSingleRow('contact'),
        ];
        return view('livewire.site.layouts.site.footer');
    }

    public function registerEmail()
    {
        $this->validate([
            'email' => ['required','unique:news,email','email']
        ],[],[
            'email' => 'ادرس ایمیل',
        ]);
        $news = new Email();
        $news->email = $this->email;
        $news->save();
        $this->addError('email','ادرس ایمیل با موفیت ثبت شد.');
    }
}

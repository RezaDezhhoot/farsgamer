<?php

namespace App\Http\Livewire\Site\Dashboard\Profile;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use App\Models\User;
use Livewire\WithFileUploads;

class AuthComponent extends BaseComponent
{
    use WithFileUploads;
    public $user, $auth_image , $auth_note , $auth_image_pattern;
    public function mount()
    {
        $this->user = auth()->user();
        if ($this->user->status == User::CONFIRMED)
            abort(404);

        $this->auth_image_pattern = Setting::getSingleRow('auth_image_pattern');
        $this->auth_note = Setting::getSingleRow('auth_note');
        $this->auth_image = $this->user->auth_image;
    }

    public function store()
    {
        if ($this->user->status == User::NOT_CONFIRMED) {
            $this->validate([
                'auth_image' => ['required','image','mimes:jpg,jpeg,png,PNG,JPG,JPEG','max:'.Setting::getSingleRow('max_profile_image_size')],
            ],[],[
                'auth_image' => 'تصویر',
            ]);
            $this->uploadFile();
            $this->user->auth_image = $this->auth_image->store('files/auth', 'public');
            $this->user->status = User::NEW;
            $this->user->save();
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        } else {
            return $this->addError('auth_image','تصویر شمار ارسال شده است و در انتظار تایید می باشد.');
        }
    }

    public function uploadFile()
    {
        // upon form submit, this function till fill your progress bar
    }

    public function render()
    {
        return view('livewire.site.dashboard.profile.auth-component');
    }
}

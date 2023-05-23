<?php

namespace App\Http\Livewire\Admin\Profile;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\Admin\ChatList;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

class IndexProfile extends BaseComponent
{
    use WithFileUploads , ChatList;
    public $user , $header , $role , $description;
    public $full_name , $last_name , $user_name , $email , $phone  , $pass_word , $file;
    public function mount(ChatRepositoryInterface $chatRepository)
    {
        $this->user = auth()->user();
        $this->header = $this->user->name;
        $this->full_name = $this->user->name;
        $this->user_name = $this->user->user_name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->chatUserId = $this->user->id;
        $this->description = $this->user->description;
        $this->chats = $chatRepository->singleContact($this->user->id);
    }
    public function render()
    {
        return view('livewire.admin.profile.index-profile')->extends('livewire.admin.layouts.admin');
    }

    public function store(SettingRepositoryInterface $settingRepository , UserRepositoryInterface $userRepository)
    {
        $fields = [
            'full_name' => ['required', 'string','max:150'],
            'description' => ['nullable','string','max:250'],
            'user_name' => ['required', 'string' ,'max:150' ,'unique:users,user_name,'. ($this->user->id ?? 0)],
            'phone' => ['required','size:11' , 'unique:users,phone,'. ($this->user->id ?? 0)],
            'email' => ['required','email','unique:users,email,'. ($this->user->id ?? 0)],
            'file' => ['nullable','image','mimes:jpg,jpeg,png,PNG,JPG,JPEG','max:'.($settingRepository->getSiteFaq('max_profile_image_size') ?? 2048)],
        ];
        $messages = [
            'full_name' => 'نام ',
            'description' => 'بایوگرافی',
            'user_name' => 'نام کربری',
            'phone' => 'شماره همراه',
            'email' => 'ایمیل',
            'file' => 'تصویر پروفایل',
        ];
        if (isset($this->pass_word))
        {
            $fields['pass_word'] = ['required','min:'.($settingRepository->getSiteFaq('password_length') ?? 5)];
            $messages['pass_word'] = 'گذرواژه';
        }
        $this->validate($fields,[],$messages);
        $this->uploadFile();
        if (!is_null($this->file)) {
            if (!is_null($this->user->profile_image))
                @unlink($this->user->profile_image);

            $this->user->profile_image  = 'storage/'.$this->file->store('profiles', 'public');
            $this->imageWatermark($this->user->profile_image);

            unset($this->file);
        }

        $this->user->name = $this->full_name;
        $this->user->description = $this->description;
        $this->user->user_name = $this->user_name;
        $this->user->phone = $this->phone;
        $this->user->email = $this->email;
        if (isset($this->pass_word))
            $this->user->password = Hash::make($this->pass_word);

        $userRepository->save($this->user);

        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function uploadFile()
    {
        // upon form submit, this function till fill your progress bar
    }

}

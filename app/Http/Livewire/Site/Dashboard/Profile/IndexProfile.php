<?php

namespace App\Http\Livewire\Site\Dashboard\Profile;

use App\Http\Livewire\BaseComponent;
use App\Models\Category;
use App\Models\Setting;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

class IndexProfile extends BaseComponent
{
    use WithFileUploads;
    public $user, $name  , $user_name , $profile_image ,$description ,$pass_word , $password_confirmation , $email,
    $phone , $province , $city , $data = [] , $file;

    public function mount()
    {
        $this->user = auth()->user();
        $this->name = $this->user->name;
        $this->user_name = $this->user->user_name;
        $this->profile_image = $this->user->profile_image;
        $this->description = $this->user->description;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->province = $this->user->province;
        $this->city = $this->user->city;
    }
    public function render()
    {
        $this->data['province'] = Setting::getProvince()[$this->province];
        $this->data['city'] = Setting::getCity()[$this->province];
        return view('livewire.site.dashboard.profile.index-profile');
    }

    public function store()
    {
        $location = false;
        $fields = [
            'name' => ['required', 'string','max:120'],
            'user_name' => ['required', 'string' ,'max:80', 'unique:users,user_name,'. ($this->user->id ?? 0)],
            'email' => ['required','email','max:250','unique:users,email,'. ($this->user->id ?? 0)],
            'profile_image' => ['nullable','image','mimes:jpg,jpeg,png','max:'.(Setting::getSingleRow('max_profile_image_size') ?? 2048)],
            'description' => ['nullable','string','max:250'],
        ];
        $message = [
            'name' => 'نام ',
            'user_name' => 'نام کربری',
            'email' => 'ایمیل',
            'profile_image' => 'تصویر پروفایل',
            'description' => 'بایوگرافی',
        ];
        $open_transactions = OrderTransaction::with(['order'])->where('status',OrderTransaction::WAIT_FOR_SEND)->
        where(function ($query){
            $query->where('seller_id',$this->user->id)->orWhere('customer_id',$this->user);
        })->whereHas('order',function ($query){
            return $query->whereHas('category',function ($query){
                return $query->where('type',Category::PHYSICAL);
            });
        })->count();

        if (isset($this->pass_word)) {
            $fields['pass_word'] = ['required','min:'.(Setting::getSingleRow('password_length') ?? 5),'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/','confirmed'];
            $message['pass_word'] = 'گذرواژه';
        }

        if ($open_transactions > 0 && ($this->city <> $this->user->city || $this->province <> $this->user->province)) {
            return $this->addError('error','ویرایش استان و شهر به دلیل داشتن معاملات باز امکان پذیر نمی باشد');
        } else {
            $fields['province'] = ['required','max:150','in:'.implode(',',$this->data['province'])];
            $fields['city'] = ['required','max:150','in:'.implode(',',$this->data['city'])];
            $message['province'] = 'استان';
            $message['city'] = 'شهر';
            $location = true;
        }
        $this->validate($fields,[],$message);

        if (isset($this->pass_word))
            $this->user->pass_word = Hash::make($this->pass_word);

        if ($location){
            $this->user->province = $this->province;
            $this->user->city = $this->city;
        }

        $this->uploadFile();
        if (!is_null($this->file)) {
            if (!is_null($this->user->profile_image))
                @unlink($this->user->profile_image);

            $this->user->profile_image  = 'storage/'.$this->file->store('profiles', 'public');
            $this->imageWatermark($this->user->profile_image);

            unset($this->file);
        }
        $this->user->name = $this->name;
        $this->user->user_name = $this->user_name;
        $this->user->email = $this->email;
        $this->user->description = $this->description;
        $this->user->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function uploadFile()
    {
        // upon form submit, this function till fill your progress bar
    }
}

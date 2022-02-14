<?php

namespace App\Http\Livewire\Site\Dashboard\Profile;

use App\Models\Setting;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class IndexProfile extends Component
{
    use WithFileUploads;
    public $user, $first_name , $last_name , $user_name , $profile_image ,$description ,$pass_word , $confirm_pass_word , $email,
    $phone , $province , $city , $data = [];

    public function mount()
    {
        $this->user = auth()->user();
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
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
            'first_name' => ['required', 'string','max:50'],
            'last_name' => ['required','string','max:50'],
            'user_name' => ['required', 'string' ,'max:80', 'unique:users,user_name,'. ($this->user->id ?? 0)],
            'email' => ['required','email','max:250','unique:users,email,'. ($this->user->id ?? 0)],
            'profile_image' => ['nullable','image','mimes:jpg,jpeg,png,svg,gif','max:2048'],
            'description' => ['nullable','string','max:250'],
        ];
        $message = [
            'first_name' => 'نام ',
            'last_name' => 'نام خانوادگی',
            'user_name' => 'نام کربری',
            'email' => 'ایمیل',
            'profile_image' => 'تصویر پروفایل',
            'description' => 'بیوگرافی',
        ];
        $open_transactions = $this->user->transactions()->where([
            ['status','!=',OrderTransaction::IS_COMPLETED],
            ['status','!=',OrderTransaction::IS_CANCELED],
        ])->count();
        if (isset($this->pass_word)) {
            $fields['pass_word'] = ['required','min:'.Setting::getSingleRow('password_length'),'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/'];
            $message['pass_word'] = 'گذرواژه';
        }
        if ($open_transactions > 0 && ($this->city <> $this->user->city || $this->province <> $this->user->province)) {
            $this->addError('error','ویرایش استان و شهر به دلیل داشتن معاملات باز امکان پذیر نمی باشد');
            return;
        } else {
            $fields['province'] = ['required','max:150','in:'.implode(',',$this->data['province'])];
            $fields['city'] = ['required','max:150','in:'.implode(',',$this->data['city'])];
            $message['province'] = 'استان';
            $message['city'] = 'شهر';
            $location = true;
        }
        $this->validate($fields,[],$message);

        if (isset($this->pass_word) && $this->pass_word == $this->confirm_pass_word)
            $this->user->pass_word = Hash::make($this->pass_word);
        elseif (isset($this->pass_word) && $this->pass_word <> $this->confirm_pass_word){
            $this->addError('pass_word','تایید گذرواژه نامعتبر');
            return;
        }
        if ($location === true){
            $this->user->province = $this->province;
            $this->user->city = $this->city;
        }
        $this->user->first_name = $this->first_name;
        $this->user->last_name = $this->last_name;
        $this->user->user_name = $this->user_name;
        $this->user->email = $this->email;
        $this->user->profile_image = $this->profile_image->store('files/users', 'public');
        $this->user->description = $this->description;
        $this->user->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

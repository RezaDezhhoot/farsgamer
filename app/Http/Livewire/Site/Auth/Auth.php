<?php

namespace App\Http\Livewire\Site\Auth;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Illuminate\Support\Facades\Hash;
use App\Http\Livewire\BaseComponent;
use Illuminate\Support\Facades\RateLimiter;

class Auth extends BaseComponent
{
    use TextBuilder;
    const  MODE_LOGIN = 'login' ;
    protected $queryString = ['mode'];
    public $phone , $password , $name, $otp , $mode = self::MODE_LOGIN;
    public $logo , $authImage , $sms = false , $data = [] ;
    public $passwordLabel = 'رمز عبور';
    public $email , $sent = false , $user_name;

    public function mount()
    {
        $this->logo = Setting::getSingleRow('logo');
        $this->authImage = Setting::getSingleRow('authImage');
    }

    public function render()
    {
        return view('livewire.site.auth.login')->extends('livewire.site.auth.app');
    }

    public function login()
    {
        $rateKey = 'verify-attempt:' . $this->phone . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, Setting::getSingleRow('dos_count') ?? 10)) {
            $this->resetInputs();
            return
                $this->addError('phone', 'زیادی تلاش کردید. لطفا پس از مدتی دوباره تلاش کنید.');
        }
        RateLimiter::hit($rateKey, 3 * 60 * 60);
        $this->resetErrorBag();
        $this->validate([
            'phone' => ['required','string'],
            'password' => ['required']
        ],[],[
            'phone' => 'شماره همراه یا نام کاربری',
            'password' => 'رمز عبور',
        ]);

        $user = User::where('phone', $this->phone)->orWhere('user_name',$this->phone)->first();

        if (!is_null($user)) {
            if (Hash::check($this->password, $user->password) || ( !is_null($user->otp) && Hash::check($this->password, $user->otp) && $this->sms === true)) {
                \Illuminate\Support\Facades\Auth::login($user,true);
                request()->session()->regenerate();
                $user->otp = null;
                $user->save();
                RateLimiter::clear($rateKey);
                $send = new SendMessages();
                $message = $this->createText('login',$user);
                $send->sends($message,$user,Notification::AUTH,$user->id);
                if ( \Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                    return redirect()->intended(route('admin.dashboard'));
            } else
                return $this->addError('password','گذواژه یا شماره همراه اشتباه می باشد.');
        }
    }


    private function resetInputs()
    {
        $this->reset(['phone', 'password']);
    }

    public function sendSMS()
    {
        $rateKey = 'verify-attempt:' . $this->phone . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, Setting::getSingleRow('dos_count'))) {
            $this->resetInputs();
            return $this->addError('phone', 'زیادی تلاش کردید. لطفا پس از مدتی دوباره تلاش کنید.');
        }
        RateLimiter::hit($rateKey, 3 * 60 * 60);
        $this->validate([
            'phone' => ['required','string'],
        ],[],[
            'phone' => 'شماره همراه یا نام کاربری',
        ]);
        $this->sms = true;
        $rand = rand(12345,999998);
        $this->otp = Hash::make($rand);
        $user = User::where('phone', $this->phone)->orWhere('user_name',$this->phone)->first();
        if (!is_null($user)) {
            $user->otp = $this->otp;
            $this->passwordLabel = 'رمز ارسال شده را وارد نماید';
            $user->save();
            $send = new SendMessages();
            $send->sendCode($rand,$user);
            $this->sent = true;
        } else
            return $this->addError('phone','این شماره همراه یافت نشد.');
    }
}

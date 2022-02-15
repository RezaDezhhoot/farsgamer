<?php

namespace App\Http\Livewire\Site\Auth;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\Admin\TextBuilder;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Hash;
use App\Http\Livewire\BaseComponent;
use Illuminate\Support\Facades\RateLimiter;
use App\Sends\SendMessages;

class Auth extends BaseComponent
{
    use TextBuilder;
    const MODE_REGISTER = 'register' ,  MODE_LOGIN = 'login' , MODE_VERIFY = 'verify';
    protected $queryString = ['mode'];
    public $username , $phone , $password , $otp , $mode = self::MODE_LOGIN;
    public $logo , $authImage , $sms = false , $data = [] , $password_confirmation;
    public $city , $province  , $passwordLabel = 'رمز عبور';
    public $first_name , $last_name , $email , $phone_number , $user_name ;

    public function mount()
    {
        SEOMeta::setTitle('احراز هویت',false);
        SEOMeta::setDescription(Setting::getSingleRow('seoDescription'));
        SEOMeta::addKeyword(explode(',',Setting::getSingleRow('seoKeyword')));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle('احراز هویت');
        OpenGraph::setDescription(Setting::getSingleRow('seoDescription'));
        TwitterCard::setTitle('احراز هویت');
        TwitterCard::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::setTitle('احراز هویت');
        JsonLd::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::addImage(Setting::getSingleRow('logo'));
        $this->logo = Setting::getSingleRow('logo');
        $this->authImage = Setting::getSingleRow('authImage');
        $this->data['province'] = Setting::getProvince();
        if ($this->mode == self::MODE_REGISTER) {
            SEOMeta::setTitle('ثبت نام', false);
            OpenGraph::setTitle('ثبت نام');
            TwitterCard::setTitle('ثبت نام');
            JsonLd::setTitle('ثبت نام');
        }
    }

    public function render()
    {
        $this->data['city'] = $this->province ? Setting::getCity()[$this->province] : [];
        return view('livewire.site.auth.auth')->extends('livewire.site.layouts.auth.auth');
    }

    public function login()
    {
        $rateKey = 'verify-attempt:' . $this->phone . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, Setting::getSingleRow('dos_count'))) {
            $this->resetInputs();
            return
                $this->addError('phone', 'زیادی تلاش کردید. لطفا پس از مدتی دوباره تلاش کنید.');
        }
        RateLimiter::hit($rateKey, 3 * 60 * 60);
        $this->resetErrorBag();
        $this->validate([
            'phone' => ['required','string','size:11'],
            'password' => ['required']
        ],[],[
            'phone' => 'شماره همراه',
            'password' => 'رمز عبور',
        ]);
        $user = User::where('phone', $this->phone)->first();
        if (!is_null($user)) {
            if ($user->status == User::NEW || $user->status == User::NOT_CONFIRMED) {
                $this->sendSMS();
                if (Hash::check($this->password, $user->otp) && $this->sms === true) {
                    $user->status = User::CONFIRMED;
                    $user->save();
                    \Illuminate\Support\Facades\Auth::login($user,true);
                    request()->session()->regenerate();
                    RateLimiter::clear($rateKey);
                    $send = new SendMessages();
                    $message = $this->createText('login',$user);
                    $send->sends($message,$user,Notification::AUTH,$user->id);

                    if ( \Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                        return redirect()->intended(route('admin.dashboard'));
                    else
                        return redirect()->intended(route('user.dashboard'));
                } else
                    return $this->addError('password','کد تایید یا شماره همراه اشتباه می باشد.');
            } else {
                if (Hash::check($this->password, $user->pass_word) || (Hash::check($this->password, $user->otp) && $this->sms === true)) {
                    \Illuminate\Support\Facades\Auth::login($user,true);
                    request()->session()->regenerate();
                    RateLimiter::clear($rateKey);
                    $send = new SendMessages();
                    $message = $this->createText('login',$user);
                    $send->sends($message,$user,Notification::AUTH,$user->id);
                    if ( \Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                        return redirect()->intended(route('admin.dashboard'));
                    else
                        return redirect()->intended(route('user.dashboard'));
                } else
                    return $this->addError('password','گذواژه یا شماره همراه اشتباه می باشد.');
            }
        } else
            return $this->addError('phone','این شماره همراه یافت نشد.');
    }

    public function register()
    {
        $this->validate(
            [
                'first_name' => ['required','string','max:120'],
                'last_name' => ['required','string','max:120'],
                'email' => ['required','email','max:160','unique:users,email'],
                'phone_number' => ['required','string','size:11','unique:users,phone'],
                'user_name' => ['required','string','max:70','unique:users,user_name'],
                'province' => ['required','in:'.implode(',',array_keys($this->data['province']))],
                'city' => ['required','in:'.implode(',',array_keys($this->data['city']))],
//                'password'=>['required','min:8','regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/','confirmed'],
            ] , [] ,[
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'email' => 'ایمیل',
            'phone_number' => 'شماره همراه',
            'user_name' => 'نام کاربری',
            'province' => 'استان',
            'city' => 'شهر',
//            'password' => 'رمز عبور',
        ]);
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone_number,
            'user_name' => $this->user_name,
            'province' => $this->province,
            'city' => $this->city,
            'otp' => 1,
            'ip' => request()->ip(),
        ]);
        $send = new SendMessages();
        $message = $this->createText('signUp',$user);
        $send->sends($message,$user,Notification::AUTH,$user->id);
        $this->reset(['password']);
        $this->phone = $this->phone_number;
        $this->sendSMS();
        $this->mode = self::MODE_LOGIN;
    }

    private function resetInputs()
    {
        $this->reset(['username', 'phone', 'password']);
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
            'phone' => ['required','string','size:11'],
        ],[],[
            'phone' => 'شماره همراه',
        ]);
        $this->sms = true;
        $rand = rand(1234,9879);
        $this->otp = Hash::make($rand);
        $user = User::where('phone', $this->phone)->first();
        if (!is_null($user)) {
            $user->otp = $this->otp;
            $this->passwordLabel = 'رمز ارسال شده را وارد نماید';
            $user->save();
            $send = new SendMessages();
            $send->sendCode($rand,$user);
        } else
            return $this->addError('phone','این شماره همراه یافت نشد.');
    }
}

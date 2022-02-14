<?php

namespace App\Http\Livewire\Site\Auth;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class Auth extends BaseComponent
{
    const MODE_REGISTER = 'register';
    const MODE_LOGIN = 'login';
    const MODE_VERIFY = 'verify';

    public $mode = self::MODE_LOGIN;

    public $username;
    public $phone;
    public $password;
    public $passwords , $passwords_confirm;
    public $otp;
    public $is_registration = false;

    public function render()
    {
        return view('livewire.site.auth.auth')->extends('livewire.site.layouts.auth.auth');
    }

    public function login()
    {
        $rateKey = 'verify-attempt:' . $this->username . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, Setting::getSingleRow('dos_count'))) {
            $this->resetInputs();

            return $this->addError('mobile', 'زیادی تلاش کردید. لطفا پس از مدتی دوباره تلاش کنید.');
        }


        RateLimiter::hit($rateKey, 12 * 60 * 60);
        $user = User::where('user_name', $this->username)->first();
        if (Hash::check($this->password, $user->pass_word))
        {
            \Illuminate\Support\Facades\Auth::login($user,true);
            request()->session()->regenerate();
            \Cart::session($user->id);
            RateLimiter::clear($rateKey);
            if ( auth()->user()->hasRole('admin'))
                    return redirect()->intended(route('admin.dashboard'));
            else{}
//                    return redirect()->route('admin.dashboard');
//            return redirect()->intended(route('auth'));
        }

    }

    public function register()
    {
        \Cart::session(4);
    }

    public function verify()
    {

    }
    private function resetInputs()
    {
        $this->reset(['username', 'phone', 'password']);
    }
}

<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User;
use App\Models\User as UserModel;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Sends\SendMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private $settingRepository , $userRepository;

    public function __construct(
        SettingRepositoryInterface $settingRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->settingRepository = $settingRepository;
        $this->userRepository = $userRepository;
    }

    public function register(Request $request)
    {
        $rateKey = 'verify-attempt:' . $request['phone'] . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, $this->settingRepository->getSiteFaq('dos_count') ?? 10)) {
            return
                response([
                    'data' => [
                        'message' => 'زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.'
                    ],
                    'status' => 'error'
                ],Response::HTTP_UNAUTHORIZED);
        }
        RateLimiter::hit($rateKey, 3 * 60 * 60);
        $request['status'] = UserModel::NEW;
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:250',
            'phone' => 'required|size:11|unique:users,phone',
            'user_name' => 'required|string|max:250|unique:users,user_name',
            'email' => 'required|string|email|unique:users,email',
        ],[],[
            'name' => 'نام',
            'phone' => 'شماره همراه',
            'user_name' => 'نام کاربری',
            'email' => 'ایمیل',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userRepository->create($request->all());
        Auth::login($user);
        $token = auth()->user()->createToken('auth_token')->plainTextToken;
        return response([
            'data'=> new User(auth()->user() , $token),
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $rateKey = 'verify-attempt:' . $request['phone'] . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, $this->settingRepository->getSiteFaq('dos_count') ?? 10)) {
            return
                response([
                    'data' => [
                        'message' => 'زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.'
                    ],
                    'status' => 'error'
                ],Response::HTTP_UNAUTHORIZED);
        }
        RateLimiter::hit($rateKey, 3 * 60 * 60);
        $validator = Validator::make($request->all(),[
            'phone' => 'required|size:11|exists:users,phone',
            'password' => 'required'
        ],[],[
            'phone' => 'شماره همراه',
            'password' => 'کله عبور'
        ]);
        if ($validator->fails()){
            return response([
                'data' => [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNAUTHORIZED);
        }
        $user = $this->userRepository->getUser('phone',$request['phone']);

        if ((!empty($user->otp) && password_verify($request['password'],$user->otp)) || password_verify($request['password'],$user->password)) {
            Auth::login($user);
            $token = auth()->user()->createToken('auth_token')->plainTextToken;
            $this->userRepository->update($user,['otp' => null]);
            RateLimiter::clear($rateKey);
            return response([
                'data'=> new User(auth()->user() , $token),
                'status' => 'success'
            ],Response::HTTP_OK);
        }
        return response([
            'data' => [
                'message' => 'اطلاعات نادرست'
            ],
            'status' => 'error'
        ],Response::HTTP_UNAUTHORIZED);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'data' => [
                'message' => 'خروج از سیستم با موفقیت انجام شد',
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function sendSMS(Request $request)
    {
        $rateKey = 'verify-attempt:' . $request['phone'] . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, $this->settingRepository->getSiteFaq('dos_count') ?? 10)) {
            return
                response([
                    'data' => [
                        'message' => 'زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.'
                    ],
                    'status' => 'error'
                ],Response::HTTP_UNAUTHORIZED);
        }
        RateLimiter::hit($rateKey, 3 * 60 * 60);
        $validator = Validator::make($request->all(),[
            'phone' => 'required|size:11|exists:users,phone',
        ],[],[
            'phone' => 'شماره همراه',
        ]);
        if ($validator->fails()){
            return response([
                'data' => [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNAUTHORIZED);
        }

        $rand = mt_rand(12345,999998);
        $otp = Hash::make($rand);
        $user = $this->userRepository->getUser('phone',$request['phone']);
        $this->userRepository->update($user,['otp'=>$otp]);
        $send = new SendMessages();
        $send->sendCode($rand,$user);
        return response([
            'data' => [
                'message' => 'کد تایید با موفقیت ارسال شد'
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

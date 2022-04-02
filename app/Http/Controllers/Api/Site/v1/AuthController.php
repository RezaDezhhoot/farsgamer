<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use TextBuilder;
    public $send;
    private $settingRepository , $userRepository , $notificationRepository;

    public function __construct(
        SettingRepositoryInterface $settingRepository,
        UserRepositoryInterface $userRepository,
        NotificationRepositoryInterface $notificationRepository
    )
    {
        $this->settingRepository = $settingRepository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
        $this->send = new SendMessages();
    }

    public function register(Request $request)
    {
        $rateKey = 'verify-attempt:' . $request['phone'] . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, $this->settingRepository->getSiteFaq('dos_count') ?? 10)) {
            return
                response([
                    'data' => [
                        'message' => [
                            'user' => ['زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.']
                        ]
                    ],
                    'status' => 'error'
                ],Response::HTTP_TOO_MANY_REQUESTS);
        }
        RateLimiter::hit($rateKey, 3 * 60 * 60);
        $request['status'] = $this->userRepository->newStatus();
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:250',
            'phone' => 'required|size:11|unique:users,phone',
            'user_name' => 'required|string|max:250|unique:users,user_name',
            'email' => 'required|string|max:150|email|unique:users,email',
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
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->userRepository->create($request->all());
        Auth::login($user);
        $token = 'Bearer '.auth()->user()->createToken('auth_token')->plainTextToken;
        $message = $this->createText('signUp',$user);
        $this->send->sends($message,$user,$this->notificationRepository->authStatus(),$user->id);
        return response([
            'data'=> [
                'register' => [
                    'user' => new User(auth()->user() , $token),
                ],
                'message' => ['عملیات ثبت نام با موفقیت انجام شد.']
            ],
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
                        'message' => [
                            'user' => ['زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.']
                        ]
                    ],
                    'status' => 'error'
                ],Response::HTTP_TOO_MANY_REQUESTS);
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
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user = $this->userRepository->getUser('phone',$request['phone']);

        if ((!empty($user->otp) && password_verify($request['password'],$user->otp)) || password_verify($request['password'],$user->password)) {
            Auth::login($user);
            $token = 'Bearer '.auth()->user()->createToken('auth_token')->plainTextToken;
            $this->userRepository->update($user,['otp' => null]);
            RateLimiter::clear($rateKey);
            $message = $this->createText('login',$user);
            $this->send->sends($message,$user,$this->notificationRepository->authStatus(),$user->id);
            return response([
                'data'=> [
                    'login' => [
                        'user' => new User(auth()->user() , $token),
                    ],
                    'message' => ['عملیات ورود با موفقیت انجام شد.']
                ],
                'status' => 'success'
            ],Response::HTTP_OK);
        }
        return response([
            'data' => [
                'message' => [
                    'phone' => ['اطلاعات نادرست'],
                    'password' => ['اطلاعات نادرست'],
                ]
            ],
            'status' => 'error'
        ],Response::HTTP_TOO_MANY_REQUESTS);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'data' => [
                'message' => [
                    'user' => [ 'خروج از سیستم با موفقیت انجام شد',]
                ]
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
                        'message' => [
                            'user' => ['زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.']
                        ]
                    ],
                    'status' => 'error'
                ],Response::HTTP_TOO_MANY_REQUESTS);
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
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rand = mt_rand(12345,999998);
        $otp = Hash::make($rand);
        $user = $this->userRepository->getUser('phone',$request['phone']);
        $this->userRepository->update($user,['otp'=>$otp]);
        $this->send->sendCode($rand,$user);
        return response([
            'data' => [
                'message' => [
                    'phone' => ['کد تایید با موفقیت ارسال شد']
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

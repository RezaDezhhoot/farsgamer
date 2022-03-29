<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CommentCollection;
use App\Http\Resources\v1\OrderCollection;
use App\Http\Resources\v1\User;
use App\Repositories\Interfaces\OffendRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private $userRepository , $settingRepository , $offendRepository;

    public function __construct(
        UserRepositoryInterface $userRepository ,
        SettingRepositoryInterface $settingRepository,
        OffendRepositoryInterface $offendRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
        $this->offendRepository = $offendRepository;
    }

    public function __invoke($user)
    {
        $user_object = $this->userRepository->getUser('user_name',$user);
        return response([
            'data' => [
                'user' => [
                    'record' => new User($user_object)
                ],
                'orders' => [
                    'records' => new OrderCollection($this->userRepository->getMyOrders($user_object,false))
                ],
                'comments' => [
                    'records' => new CommentCollection($this->userRepository->getMyComments($user_object))
                ],
                'offend' => [
                    'subjects' => $this->settingRepository->getSubjects('offends',[])
                ],
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }

    public function sendOffend($user , Request $request)
    {
        $user_object = $this->userRepository->getUser('user_name',$user);
        $rateKey = 'verify-attempt:' . $request['phone'] . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 4)) {
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
        $request['user_id'] = $user_object->id;
        $validator = Validator::make($request->all(),[
            'phone' => 'required|size:11|exists:users,phone',
            'subject' => 'required|max:95|string',
            'content' => 'required|max:1600|string'
        ],[],[
            'phone' => 'شماره همراه',
            'subject' => 'موضوع',
            'content' => 'متن',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->offendRepository->create($request->all());
        return response([
            'data' =>  [
                'message' => [
                    'content' => ['گزارش با موفقیت ثبت شد.']
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

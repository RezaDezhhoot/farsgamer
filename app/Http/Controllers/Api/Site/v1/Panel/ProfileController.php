<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Filesystem\Filesystem;

class ProfileController extends Controller
{
    private $settingRepository , $transactionRepository , $userRepository;

    public function __construct
    (
        SettingRepositoryInterface $settingRepository,
        OrderTransactionRepositoryInterface $transactionRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->settingRepository = $settingRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request)
    {
        return response([
            'data' => [
                'user' => new User(auth()->user()),
                'details' => [
                    'minimum_password_length' => $this->settingRepository->getSiteFaq('password_length') ?? 5,
                    'max_profile_image_size' => $this->settingRepository->getSiteFaq('max_profile_image_size') ?? 2048,
                    'valid_profile_image_formats' => 'jpg,jpeg,png',
                    'auth_descriptions' => $this->settingRepository->getSiteFaq('auth_note'),
                    'auth_image_pattern' => asset($this->settingRepository->getSiteFaq('auth_image_pattern')),
                    'provinces'=> $this->settingRepository::getProvince(),
                    'cities' => $this->settingRepository->getCities(),
                ]
            ] , 'status' => 'success'
        ] , Response::HTTP_OK);
    }

    public function update(Request $request , Filesystem $filesystem)
    {
        $user = $this->userRepository->find(auth()->id());
        $location = false;
        $fields = [
            'name' => ['required', 'string','max:120'],
            'user_name' => ['required', 'string' ,'max:80', 'unique:users,user_name,'. ($user->id ?? 0)],
            'email' => ['required','email','max:250','unique:users,email,'. ($user->id ?? 0)],
            'profile_image' => ['nullable','image','mimes:jpg,jpeg,png','max:'.($this->settingRepository->getSiteFaq('max_profile_image_size') ?? 2048)],
            'description' => ['nullable','string','max:250'],
        ];
        $message = [
            'name' => '?????? ',
            'user_name' => '?????? ??????????',
            'email' => '??????????',
            'profile_image' => '?????????? ??????????????',
            'description' => '??????????????????',
        ];
        $user_transactions = $this->transactionRepository->getUserTransactions($user);

        if (isset($request['password'])) {
            $fields['password'] = ['required','min:'.($this->settingRepository->getSiteFaq('password_length') ?? 5),'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/'];
            $message['password'] = '??????????????';
        }
        if ($user_transactions > 0 && ($request['city'] <> $user->city || $request['province'] <> $user->province)) {
            return response([
                'data' => [
                    'message' => [
                        'province' => ['???????????? ?????????? ?? ?????? ???? ???????? ?????????? ?????????????? ?????? ?????????? ???????? ?????? ????????'],
                        'city' => ['???????????? ?????????? ?? ?????? ???? ???????? ?????????? ?????????????? ?????? ?????????? ???????? ?????? ????????'],
                    ]
                ], 'status' => 'error'
            ],Response::HTTP_NOT_ACCEPTABLE);
        } else if (isset($request['province']) || isset($request['city'])) {
            if (!in_array($request['province'],array_keys($this->settingRepository::getProvince())))
                return response([
                    'data' => [
                        'message' => [
                            'province' => ['?????????? ???????????? ?????? ?????????? ?????? ????????'],
                        ]
                    ], 'status' => 'error'
                ],Response::HTTP_UNPROCESSABLE_ENTITY);

            $fields['province'] = ['required','max:150','in:'.implode(',',array_keys($this->settingRepository::getProvince()))];
            $message['province'] = '??????????';
            $fields['city'] = ['required','max:150','in:'.implode(',',array_keys($this->settingRepository->getCity($request['province'])))];
            $message['province'] = '??????????';
            $message['city'] = '??????';
            $location = true;
        }
        $validator = Validator::make($request->all(),$fields,[],$message);
        if ($validator->fails()){
            return response([
                'data' => [
                    'message' => $validator->errors()
                ], 'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (isset($request['password']))
            $user->password = Hash::make($request['password']);

        if ($location){
            $user->province = $request['province'];
            $user->city = $request['city'];
        }
        if (!empty($request->file('profile_image'))){
            if (!is_null($user->profile_image))
                @unlink($user->profile_image);

            $file = $request->file('profile_image');

            $imagePath = "app/public/profiles";
            $fileName = $file->getClientOriginalName();
            $fileName = Carbon::now()->timestamp.'-'.$fileName;

            $file->move(storage_path($imagePath),$fileName);
            $user->profile_image = "storage/profiles/{$fileName}";
            $this->imageWatermark($user->profile_image);
        }

        $user->name = $request['name'];
        $user->user_name = $request['user_name'];
        $user->email = $request['email'];
        $user->description = $request['description'];
        $this->userRepository->save($user);
        return response([
            'data' => [
                'user' => new User($user),
                'message' => [
                    'profile' => ['?????????????? ???? ???????????? ???????? ????????.'],
                ]
            ], 'status' => 'success'
        ],Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Rules\ChekCardNumber;
use App\Rules\ValidNationCode;
use App\Traits\Admin\TextBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use TextBuilder;
    public $send ;
    private $settingRepository , $transactionRepository , $userRepository , $cardRepository;

    public function __construct
    (
        SettingRepositoryInterface $settingRepository,
        OrderTransactionRepositoryInterface $transactionRepository,
        UserRepositoryInterface $userRepository,
        CardRepositoryInterface $cardRepository
    )
    {
        $this->settingRepository = $settingRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->cardRepository = $cardRepository;
    }

    public function __invoke(Request $request)
    {
        $user = auth()->user();
        if (!$this->userRepository->authenticated() && !$this->userRepository->waiting()) return response([
                'data' => [
                    'details' => [
                        'auth_descriptions' => $this->settingRepository->getSiteFaq('auth_note'),
                        'auth_image_pattern' => asset($this->settingRepository->getSiteFaq('auth_image_pattern')),
                        'provinces'=> $this->settingRepository::getProvince(),
                        'cities' => $this->settingRepository->getCities(),
                        'banks' => $this->cardRepository->getBank(),
                    ],
                    'user' => [
                        'auth_image' => asset($user->auth_image),
                        'code_id' => $user->code_id,
                        'province' => $user->province,
                        'city' => $user->city,
                        'card'=> $this->cardRepository->getFirst($user),
                    ]
                ],'status' => 'success'
            ] , Response::HTTP_OK);
        else return response([
            'data' => [
                'message' => [
                    'user' => ['کاربر قبلا احراز هویت شده است']
                ]
            ],'status' => 'error'
        ] , Response::HTTP_FORBIDDEN);
    }

    public function auth(Request $request)
    {
        if ($this->userRepository->authenticated() || $this->userRepository->waiting()) return response([
            'data' => [
                'message' => [
                    'user' => ['کاربر قبلا احراز هویت شده است']
                ]
            ],'status' => 'error'
        ] , Response::HTTP_FORBIDDEN);

        if (!isset($request['province']) || empty($request['province']) || !in_array($request['province']
                ,array_keys($this->settingRepository::getProvince()))) {
            return response([
                'data' => [
                    'message' => [
                        'province' => ['فیلد استان الزامی می باشد'],
                    ]
                ], 'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $card = $this->cardRepository->getFirst(auth()->user());
        $validator = Validator::make($request->all(),[
            'auth_image' => ['required','image','mimes:jpg,jpeg,png','max:'.($this->settingRepository->getSiteFaq('max_profile_image_size') ?? 2048)],
            'code_id' => ['required',new ValidNationCode()],
            'province' => ['required','max:150','in:'.implode(',',array_keys($this->settingRepository::getProvince()))],
            'city' => ['required','max:150','in:'.implode(',',array_keys($this->settingRepository->getCity($request['province'])))],
            'card_number' => ['required',new ChekCardNumber(),'unique:cards,card_number,'.($card->id ?? 0)],
            'card_sheba' => ['required','string','regex:/\b^(ir|IR)(\:|\-|\s)?(\d|\s|\-){23,30}\b$/','unique:cards,card_sheba,'.($card->id ?? 0)],
        ],[],[
            'auth_image' => 'تصویر احراز هویت',
            'code_id' => 'شماره ملی',
            'province' => 'استان',
            'city' => 'شهر',
            'card_number' => 'شماره کارت',
            'card_sheba' => 'شماره شبا',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $card = (string) preg_replace('/\D/','',$request['card_number']);
        $bank_code = substr($card,0,6);
        $card = [
            'user_id' => auth()->id(),
            'card_number' => preg_replace('/\D/','',$request['card_number']),
            'card_sheba' => preg_replace('/\D/','',$request['card_sheba']),
            'bank' => $bank_code,
            'status' => $this->cardRepository::newStatus(),
            'first' => auth()->id()
        ];
        $this->cardRepository->updateOrCreate(['first' => auth()->id()],$card);
        $image = '';
        if (!empty($request->file('auth_image'))){

            $file = $request->file('auth_image');

            $imagePath = "app/public/auth";
            $fileName = $file->getClientOriginalName();
            $fileName = Carbon::now()->timestamp.'-'.$fileName;

            $file->move(storage_path($imagePath),$fileName);
            $image = "storage/auth/{$fileName}";
        }
        $this->userRepository->update(
            $this->userRepository->find(auth()->id()),
            [
                'auth_image' => $image,
                'code_id' => $request['code_id'],
                'province' => $request['province'],
                'city' => $request['city'],
                'status' => $this->userRepository::waitToConfirmStatus(),
                'ip' => $request->ip(),
            ]
        );
        return response([
            'data' => [
                'message' => [
                    'auth' => ['اطلاعات با موفقیت ارسال شد.'],
                ]
            ], 'status' => 'success'
        ],Response::HTTP_OK);
    }
}

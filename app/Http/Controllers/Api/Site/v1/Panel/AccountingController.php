<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Panel\RequestCollection;
use App\Models\OrderTransaction;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use App\Http\Resources\v1\Panel\Request as RequestResource;
class AccountingController extends Controller
{
    private $requestRepository , $cardRepository , $userRepository , $settingRepository , $paymentRepository;
    public function __construct(
        RequestRepositoryInterface $requestRepository ,
        CardRepositoryInterface $cardRepository ,
        UserRepositoryInterface $userRepository ,
        SettingRepositoryInterface $settingRepository ,
        PaymentRepositoryInterface $paymentRepository
    )
    {
        $this->requestRepository = $requestRepository;
        $this->cardRepository = $cardRepository;
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function details()
    {
        return response([
            'data' => [
                'details' => [
                    'total_inventory' => number_format(Auth::user()->inventory_being_traded + Auth::user()->balance),
                    'inventory_being_traded' => number_format(Auth::user()->inventory_being_traded),
                    'removable_inventory' => number_format(Auth::user()->balance),
                    'gateways' => [
                        'payir' => 'پی',
                        'zarinpal' => 'زرین پال'
                    ]
                ]
            ],
            'status' => 'success'
        ] , Response::HTTP_OK);
    }

    public function index(Request $request)
    {
        $requests = $this->requestRepository->getUserRequests(Auth::user());
        return response([
            'data' => [
                'requests' => [
                    'records' => new RequestCollection($requests),
                    'paginate' => [
                        'total' => $requests->total(),
                        'count' => $requests->count(),
                        'per_page' => $requests->perPage(),
                        'current_page' => $requests->currentPage(),
                        'total_pages' => $requests->lastPage()
                    ],
                ],
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }


    public function charge(Request $request)
    {
        $transaction = $request->has('orders_transaction_id') ? OrderTransaction::query()->where('status',OrderTransaction::WAIT_FOR_PAY)->findOrFail( $request->input('orders_transaction_id')) : null;
        $validator = Validator::make($request->all(),[
            'price' => $transaction ? 'required|numeric|size:'.$transaction->price : 'required|numeric|min:1000|max:999999999999999999999999.99999999999999',
            'gateway' => ['required','in:payir,zarinpal'],
            'call_back_address' => ['required','url','max:255'],
        ],[],[
            'price' => 'مبلغ',
            'gateway' => 'درگاه',
            'call_back_address' => 'ادرس'
        ]);
        if ($validator->fails()){
            return response([
                'data' => [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $payment = Payment::via($request['gateway'])->callbackUrl(env('APP_URL') . '/verify/'. $request['gateway'])
                ->purchase((new Invoice)
                    ->amount((int)$request['price']), function ($driver,$transactionId) use ($request) {
                    $this->store($request,$request['gateway'] ,$transactionId);
                })->pay()->toJson();
            $payment = json_decode($payment);

            return response([
                'data' => [
                    'gateway' => [
                        'link' => $payment->action
                    ]
                ],
                'status' => 'success'
            ],Response::HTTP_OK);
        } catch (PurchaseFailedException $exception) {
            return response([
                'data' => [
                    'message' => [
                        'gateway' => [$exception->getMessage()]
                    ]
                ],
                'status' => 'error'
            ],Response::HTTP_BAD_GATEWAY);
        }
    }

    private function store($request,$gateway, $transactionId = null)
    {
        return  DB::transaction(function () use ($gateway, $transactionId , $request) {
            $pay = $this->paymentRepository->create(Auth::user(),[
                'amount' => $request['price'],
                'payment_gateway' => $gateway,
                'payment_token' => $transactionId,
                'model_type' => 'user',
                'model_id' => Auth::id(),
                'user_id' => Auth::id(),
                'status_code' => 8,
                'call_back_url' => $request['call_back_address'],
                'orders_transaction_id' => $request->has('transaction_id') ? $request->input('transaction_id') : null
            ]);
            return $pay->id;
        });
    }

    public function request(Request $request)
    {
        $user = $this->userRepository->find(Auth::id());
        $max = $user->balance;
        $validator = Validator::make($request->all(),[
            'price' => 'required|numeric|min:'.($this->settingRepository->getSiteFaq('min_price_to_request') ?? 10000).'|max:'.$max,
            'card' => ['required', 'test' => Rule::exists('cards','id')->where(function ($query) {
                return $query->where([
                    ['user_id',Auth::id()],
                    ['status',$this->cardRepository::confirmStatus()],
                ]);
            })]
        ],[
            'exists' => 'حساب انتخابی تایید نشده است.'
        ],[
            'price' => 'مبلغ',
            'card' => 'حساب بانکی'
        ]);
        if ($validator->fails()){
            return response([
                'data' => [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $my_request = [
            'price' => $request['price'],
            'card_id' => $request['card'],
            'status' => $this->requestRepository::newStatus(),
        ];
        try {
            $user->withdraw($request['price'], ['description' => 'درخواست تسویه حساب', 'from_admin'=> true]);
            $new_request = $this->requestRepository->create($user,$my_request);
        } catch (BalanceIsEmpty | InsufficientFunds $exception) {
            return response([
                'data' => [
                    'wallet' => [$exception->getMessage()]
                ],
                'status' => 'error'
            ],Response::HTTP_NOT_ACCEPTABLE);
        }
        return response([
            'data' => [
                'request' => [
                    'record' => new RequestResource($new_request)
                ],
                'message' => [
                    'request' => ['درخواست تسویه حساب با موفقیت ثبت شد.']
                ]
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }

    public function show($id)
    {
        $request = $this->requestRepository->getUserRequest(Auth::user(),$id);
        return response([
            'data' => [
                'request' => [
                    'record' => new RequestResource($request)
                ],
                'message' => [
                    'request' => ['درخواست تسویه حساب با موفقیت ثبت شد.']
                ]
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }
}

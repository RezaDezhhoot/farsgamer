<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Group;
use App\Http\Resources\v1\Panel\Transaction;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Http\Resources\v1\Order as OrderResource;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderRepository , $orderTransactionRepository , $settingRepository , $chatRepository  , $userRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository ,
        OrderTransactionRepositoryInterface $orderTransactionRepository ,
        SettingRepositoryInterface $settingRepository ,
        ChatRepositoryInterface $chatRepository ,
        UserRepositoryInterface $userRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderTransactionRepository = $orderTransactionRepository;
        $this->settingRepository = $settingRepository;
        $this->chatRepository = $chatRepository;
        $this->userRepository = $userRepository;
    }

    public function show($order_id)
    {
        $order = $this->orderRepository->getOrder($order_id);
        $this->orderRepository->increment($order,'view_count',1);
        return response([
            'data' => [
                'order' => [
                    'record' => new OrderResource($order)
                ],
                'Laws' => [
                    'laws' => $this->settingRepository->getSiteLaw('law'),
                    'chatLaws' => $this->settingRepository->getSiteLaw('chatLaw'),
                ],
            ]
        ],Response::HTTP_OK);
    }

    public function startTransaction($order_id , Request $request)
    {
        $order = $this->orderRepository->getOrder($order_id);
        if ($order->user->id != auth('api')->id()) {
            if (!auth()->user()->baned) {
                if ($this->userRepository->hasTransaction($order_id))
                    return response([
                        'data' => [
                            'message' => [
                                'transaction' => ['کاربر قبلا برای این اگهی درخواست فرستاده است.']
                            ]
                        ],
                        'status' => 'error'
                    ],Response::HTTP_TOO_MANY_REQUESTS);

                $validator = Validator::make($request->all(),[
                    'confirmLaw' => 'required|boolean',
                ],[],[
                    'confirmLaw' => 'تایید قوانین',
                    ]);
                if ($validator->fails()) {
                    return response([
                        'data' => [
                            'message' => $validator->errors()
                        ],
                        'status' => 'error'
                    ],Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $commission = $this->calculateCommission($order->price,$order->category);
                $transaction = $this->orderTransactionRepository->start($order,$commission);
                return response([
                    'data' => [
                        'transaction' => gettype($transaction) == 'integer' ? [] : new Transaction($transaction,[],$this->orderTransactionRepository),
                        'message' =>  [
                            'transaction' => [
                                gettype($transaction) == 'integer' ? 'خطا در هنگام ایجاد معامله.' : 'معامله با موفقیت ایجاد شد. '
                            ]
                        ]
                    ],
                    'status' => gettype($transaction) == 'integer' ? 'error' : 'success',
                ],gettype($transaction) == 'integer' ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_OK);
            } else {
                return response([
                    'data' => [
                        'message' => [
                            'user'  => ['کاربر مسدود شده است.']
                        ]
                    ],
                    'status' => 'error',
                ],Response::HTTP_FORBIDDEN);
            }
        } else {
            return response([
                'data' => [
                    'message' => [
                        'user' => ['کاربر نمی تواند با خودش معامله انجام دهد .']
                    ]
                ],
                'status' => 'error',
            ],Response::HTTP_NOT_ACCEPTABLE);
        }
    }

}

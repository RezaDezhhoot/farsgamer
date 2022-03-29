<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Group;
use App\Http\Resources\v1\Transaction;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Http\Resources\v1\Order as OrderResource;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderRepository , $orderTransactionRepository , $settingRepository , $chatRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository ,
        OrderTransactionRepositoryInterface $orderTransactionRepository ,
        SettingRepositoryInterface $settingRepository ,
        ChatRepositoryInterface $chatRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderTransactionRepository = $orderTransactionRepository;
        $this->settingRepository = $settingRepository;
        $this->chatRepository = $chatRepository;
    }

    public function show($order_id)
    {
        $order = $this->orderRepository->getOrder($order_id);
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
                        'transaction' => $transaction == 0 ? [] : new Transaction($transaction),
                        'message' =>  [
                            'transaction' => [
                                $transaction == 0 ? 'خطا در هنگام ایجاد معامله.' : 'معامله با موفقیت ایجاد شد. '
                            ]
                        ]
                    ],
                    'status' => $transaction == 0 ? 'error' : 'success',
                ],$transaction == 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_OK);
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
            ],Response::HTTP_FORBIDDEN);
        }
    }

    public function startChat(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'confirmLaw' => 'required|boolean',
            'user_target' => 'required|exists:users,id'
        ],[],[
            'confirmLaw' => 'تایید قوانین',
            'user_target' => 'کاربر مورد نظر'
        ]);
        if ($validator->fails()) {
            return response([
                'data' => [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $group = $this->chatRepository->startChat($request['user_target']);
        return response([
            'data' => [
                'group' => [
                    'record' => new Group($group)
                ],
                'message' => [
                    'group_chats' => ['چت با موفقیت ایجاد شد']
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

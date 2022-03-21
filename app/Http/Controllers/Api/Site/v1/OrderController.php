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
        $public = [
            'title' => $order->category->title.' | '.$order->slug,
            'seoDescription' => $order->category->seo_description,
            'seoKeywords' => $order->category->seo_keywords,
            'logo' => asset($this->settingRepository->getSiteFaq('logo')),
        ];
        return response([
            'data' => [
                'order' => new OrderResource($order),
                'head' => $public,
                'Law' => $this->settingRepository->getSiteLaw('law'),
                'chatLaw' => $this->settingRepository->getSiteLaw('chatLaw'),
            ]
        ],Response::HTTP_OK);
    }

    public function startTransaction($order_id , Request $request)
    {
        $order = $this->orderRepository->getOrder($order_id);
        if ($order->user->id != auth()->id()) {
            $ban = Carbon::make(now())->diff(auth()->user()->ban)->format('%r%i');
            if ($ban <= 0) {
                $validator = Validator::make($request->all(),[
                    'confirmLaw' => 'required|boolean',
                ],[],[
                    'confirmLaw' => 'تایید قوانین',
                    ]);
                if ($validator->fails()) {
                    return \response([
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
                        'transaction' => new Transaction($transaction),
                        'message' =>  'معامله با موفقیت ایجاد شد. '
                    ],
                    'status' => 'success',
                ],Response::HTTP_OK);
            } else {
                return response([
                    'data' => [
                        'message' => 'کاربر مسدود شده است. '
                    ],
                    'status' => 'error',
                ],Response::HTTP_FORBIDDEN);
            }
        } else {
            return response([
                'data' => [
                    'message' => 'کاربر نمی تواند با خودش معامله انجام دهد .'
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
            'data' => new Group($group),
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

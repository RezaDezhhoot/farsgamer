<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Panel\Transaction;
use App\Http\Resources\v1\Panel\TransactionCollection;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    use TextBuilder;
    private $orderTransactionRepository , $userRepository , $settingRepository,$orderRepository,$paymentRepository;
    private $notificationRepository , $categoryRepository , $commentRepository;
    public function __construct(
        OrderTransactionRepositoryInterface $orderTransactionRepository,
        UserRepositoryInterface $userRepository,
        SettingRepositoryInterface $settingRepository,
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository ,
        NotificationRepositoryInterface $notificationRepository ,
        CategoryRepositoryInterface $categoryRepository ,
        CommentRepositoryInterface $commentRepository
    )
    {
        $this->orderTransactionRepository = $orderTransactionRepository;
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->notificationRepository = $notificationRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transactions = $this->orderTransactionRepository->getMyTransactions(Auth::id(),$request);
        return response([
            'data' => [
                'transactions' => [
                    'records' => new TransactionCollection($transactions),
                    'paginate' => [
                        'total' => $transactions->total(),
                        'count' => $transactions->count(),
                        'per_page' => $transactions->perPage(),
                        'current_page' => $transactions->currentPage(),
                        'total_pages' => $transactions->lastPage()
                    ]
                ],
                'details' => [
                    'tabs' => [
                        'seller','customer','all'
                    ],
                    'tab' => $request['tab'] ?? 'all',
                ]
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = $this->orderTransactionRepository->getMyTransaction(Auth::id(),$id);
        $data = [];
        $user_id = auth()->id();
        if (!$transaction->is_returned) {
            switch ($transaction->status){
                case $this->orderTransactionRepository::confirm():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => [],
                            'message' => 'درخواست معامله با شما را داده آیا این معامله را شروع میکنید؟',
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => true,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => [],
                            'message' => 'لطفا تا تایید درخواست شما از طرف فروشنده منتظر بمانیید؟',
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => true,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::pay():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => [],
                            'message' => "لطفا تا پایان زمان مقرر منتظر پرداخت وجه از طرف {$transaction->customer->user_name} باشید",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => collect(json_decode($transaction->category->forms))->where('status','normal')
                                ->where('for','customer'),
                            'message' => 'لطفا جهت ادامه معامله مبلغ  معیین شده را  زمان مقرر پرداخت کنید',
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => true,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::send():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => collect(json_decode($transaction->category->forms))->where('status','normal')
                                ->where('for','seller'),
                            'transfer_methods' => $transaction->order->category->type == $this->categoryRepository::physical() ?
                                $transaction->category->sends->map(function ($item2){
                                return [
                                    'name'=> $item2->id,
                                    'type'=> 'radio',
                                    'label'=> $item2->slug,
                                    'logo'=> asset($item2->logo),
                                    'send_time_inner_city'=> $item2->send_time_inner_city,
                                    'send_time_outer_city'=> $item2->send_time_outer_city,
                                    'description'=> $item2->note,
                                    'web_site'=> $item2->pursuit_web_site,
                                ];
                            }) : [],
                            'message' => "لطفا تا {$transaction->timer->diffForHumans()} دیگر اطلاعات محصول را وارد نمایید.",
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => true,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => [],
                            'message' =>  "لطفا تا {$transaction->timer->diffForHumans()} دیگر منتظر ارسال اطلاعات از طرف فروشنده باشید.",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::control():{
                    $data = [
                        'fields' => [],
                        'message' => "لطفا تا تایید اطلاعات محصول توسط پشتیبان منتظر بمانید.",
                        'can_continue' => false,
                        'can_continue_after_countdown' => false,
                        'can_cancel' => false,
                        'can_cancel_after_countdown' => false,
                        'can_refund_request' => false,
                        'can_comment' => false,
                    ];
                    break;
                }
                case $this->orderTransactionRepository::isReturned():{
                    $data = [
                        'fields' => [],
                        'message' => "لطفا تا بررسی درخواست مرجوعیت محصول توسط پشتیبان منتظر بمانید.",
                        'can_continue' => false,
                        'can_continue_after_countdown' => false,
                        'can_cancel' => false,
                        'can_cancel_after_countdown' => false,
                        'can_refund_request' => false,
                        'can_comment' => false,
                    ];
                    break;
                }
                case $this->orderTransactionRepository::receive():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => [],
                            'message' => "لطفا تا {$transaction->timer->diffForHumans()} دیگر منتطر تایید از طرف خریدار باشید.",
                            'can_continue' => false,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => [
                                [
                                    'type' => 'text',
                                    'name' => 'received_result',
                                    'required' => true,
                                    'width' => 12,
                                    'label' => 'در صورت عدم دریافت توضیحات لازم را وارد نمایید.',
                                    'placeholder' => '',
                                    'value' => '',
                                    'options' => [],
                                ],
                                [
                                    'type' => 'text',
                                    'name' => 'refunded_cause',
                                    'required' => true,
                                    'width' => 12,
                                    'label' => 'علت درخواست مرجوعیت.',
                                    'placeholder' => '',
                                    'value' => '',
                                    'options' => [],
                                ],
                                [
                                    'type' => 'file',
                                    'name' => 'refunded_images',
                                    'required' => true,
                                    'width' => 12,
                                    'label' => 'تصاویر محصول به عنوان سند مرجوعیت.',
                                    'placeholder' => '',
                                    'value' => '',
                                    'options' => [],
                                    'max_file_number' => 4,
                                    'valid_file_formats' => 'png|jpg|jpeg|mp4',
                                    'max_file_size' => 2048,
                                    'file_size_unit' => 'KB',
                                ]
                            ],
                            'message' =>  "خریدار محترم در نظر داشته باشید که پس از مشاهده اطلاعت نسبت به صحبت و درست بودن اطلاعات تا{$transaction->timer->diffForHumans()}آینده اقدام نمایید.",
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => true,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::noReceive():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => collect(json_decode($transaction->category->forms))->where('status','normal')
                                ->where('for','seller'),
                            'transfer_methods' => $transaction->order->category->type == $this->categoryRepository::physical() ?
                                $transaction->category->sends->map(function ($item2){
                                return [
                                    'name'=> $item2->id,
                                    'type'=> 'radio',
                                    'label'=> $item2->slug,
                                    'logo'=> asset($item2->logo),
                                    'send_time_inner_city'=> $item2->send_time_inner_city,
                                    'send_time_outer_city'=> $item2->send_time_outer_city,
                                    'description'=> $item2->note,
                                    'web_site'=> $item2->pursuit_web_site,
                                ];
                            }) : [],
                            'message' => "لطفا تا {$transaction->timer->diffForHumans()} دیگر اطلاعات محصول را مجدد وارد نمایید.",
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => true,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => [],
                            'message' =>  "لطفا مجدد تا {$transaction->timer->diffForHumans()} دیگر منتظر ارسال اطلاعات از طرف فروشنده باشید.",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::complete():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => [],
                            'message' => "تکمیل معامله",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => (is_null($transaction->comment) || empty($transaction->comment)) ? [
                                [
                                    'type' => 'text',
                                    'name' => 'comment',
                                    'required' => false,
                                    'width' => 12,
                                    'label' => 'خریدار محترم لطفا نظر خود را درمورد فروشنده و نحوه پاسخگویی ثبت کنید.',
                                    'placeholder' => 'نظرخود را وارد نمایید',
                                    'value' => '',
                                    'options' => [],
                                ],
                                [
                                    'type' => 'number',
                                    'name' => 'rate',
                                    'required' => false,
                                    'width' => 12,
                                    'label' => 'لطفا میزان امتیاز خود را نیز مشخص کنید.',
                                    'placeholder' => '',
                                    'value' => '',
                                    'options' => [
                                        1,2,3,4,5
                                    ],
                                ]
                            ] : [],
                            'message' => "تکمیل معامله",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => (is_null($transaction->comment) || empty($transaction->comment)),
                        ];
                    break;
                }
            }
        } elseif ($transaction->is_returned) {
            switch ($transaction->status){
                case $this->orderTransactionRepository::sendingData():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => collect(json_decode($transaction->category->forms))->where('status','return')
                                ->where('for','seller'),
                            'message' => "لطفا تا {$transaction->timer->diffForHumans()} دیگر اطلاعات مورد نظر را وارد نمایید یا در غیر این صورت ادامه دهید.",
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => [],
                            'message' =>  "لطفا تا {$transaction->timer->diffForHumans()} دیگر منتظر تایید فروشنده باشید.",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::send():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => [],
                            "لطفا تا{$transaction->timer->diffForHumans()} دیگر منتظر ارسال اطلاعات از طرف خریدار باشید.",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => collect(json_decode($transaction->category->forms))->where('status','normal')
                                ->where('for','seller'),
                            'transfer_methods' => $transaction->order->category->type == $this->categoryRepository::physical() ?
                                $transaction->category->sends->map(function ($item2){
                                return [
                                    'name'=> $item2->id,
                                    'type'=> 'radio',
                                    'label'=> $item2->slug,
                                    'logo'=> asset($item2->logo),
                                    'send_time_inner_city'=> $item2->send_time_inner_city,
                                    'send_time_outer_city'=> $item2->send_time_outer_city,
                                    'description'=> $item2->note,
                                    'web_site'=> $item2->pursuit_web_site,
                                ];
                            }) : [],
                            'message' => "لطفا تا{$transaction->timer->diffForHumans()} دیگر اطلاعات محصول را وارد نمایید.",
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::receive():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => [
                                [
                                    'type' => 'text',
                                    'name' => 'received_result',
                                    'required' => true,
                                    'width' => 12,
                                    'label' => 'در صورت عدم دریافت توضیحات لازم را وارد نمایید.',
                                    'placeholder' => '',
                                    'value' => '',
                                    'options' => [],
                                ]
                            ],
                            'message' =>  "فروشنده محترم در نظر داشته باشید که پس از مشاهده اطلاعت نسبت به صحبت و درست بودن اطلاعات تا{$transaction->timer->diffForHumans()}آینده اقدام نمایید.",
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => [],
                            'message' => "لطفا تا {$transaction->timer->diffForHumans()} دیگر منتطر تایید از طرف فروشنده باشید.",
                            'can_continue' => false,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::noReceive():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => [],
                            "لطفا مجدد تا{$transaction->timer->diffForHumans()} دیگر منتظر ارسال اطلاعات از طرف خریدار باشید.",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => collect(json_decode($transaction->category->forms))->where('status','normal')
                                ->where('for','seller'),
                            'transfer_methods' => $transaction->order->category->type == $this->categoryRepository::physical() ?
                                $transaction->category->sends->map(function ($item2){
                                return [
                                    'name'=> $item2->id,
                                    'type'=> 'radio',
                                    'label'=> $item2->slug,
                                    'logo'=> asset($item2->logo),
                                    'send_time_inner_city'=> $item2->send_time_inner_city,
                                    'send_time_outer_city'=> $item2->send_time_outer_city,
                                    'description'=> $item2->note,
                                    'web_site'=> $item2->pursuit_web_site,
                                ];
                            }) : [],
                            'message' => "لطفا تا{$transaction->timer->diffForHumans()} دیگر مجدد اطلاعات محصول را وارد نمایید.",
                            'can_continue' => true,
                            'can_continue_after_countdown' => true,
                            'can_cancel' => true,
                            'can_cancel_after_countdown' => true,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
                case $this->orderTransactionRepository::cancel():{
                    if ($user_id == $transaction->seller_id)
                        $data = [
                            'fields' => [],
                            'message' => "لغو معامله معامله",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    elseif ($user_id == $transaction->customer_id)
                        $data = [
                            'fields' => [],
                            'message' => "لغو معامله",
                            'can_continue' => false,
                            'can_continue_after_countdown' => false,
                            'can_cancel' => false,
                            'can_cancel_after_countdown' => false,
                            'can_refund_request' => false,
                            'can_comment' => false,
                        ];
                    break;
                }
            }
        }
        return response([
            'data' => [
                'transaction' => [
                    'record' => new Transaction($transaction,$data,$this->orderTransactionRepository),
                ],
                'details' => [
                    'statuses' => collect($this->orderTransactionRepository::getStatus($transaction->is_returned))
                        ->map(fn ($status) => $status['label']),
                ]
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $transaction = $this->orderTransactionRepository->getMyTransaction(Auth::id(),$id);
        if (in_array($transaction->status,[$this->orderTransactionRepository::cancel()]))
            return response([
                'status' => 'error'
            ] , Response::HTTP_NOT_ACCEPTABLE);

        if (in_array($transaction->status,[$this->orderTransactionRepository::control()]))
            return response([
                'data' => [
                    'message' => [
                        'transaction' => ['در حال بررسی اطلاعات ارسالی هستیم لطفا تا اعلام نتجه منتظر بمانید']
                    ],
                ],
                'status' => 'success',
            ] , Response::HTTP_OK);

        if (in_array($transaction->status,[$this->orderTransactionRepository::isReturned()]))
            return response([
                'data' => [
                    'message' => [
                        'transaction' => ['در حال بررسی درخواست مرجوعیت هستیم لطفا تا اعلام نتجه منتظر بمانید']
                    ],
                ],
                'status' => 'success',
            ] , Response::HTTP_OK);

        $sms = new SendMessages();
        $data = $transaction->data;
        $subject = $this->notificationRepository->transactionStatus();
        $timerStatus = Carbon::make(now())->diff($transaction->timer)->format('%r%s');
        if (!$transaction->is_returned){
            switch ($transaction->status)
            {
                case $this->orderTransactionRepository::confirm():{
                    if ((auth()->id() == $transaction->seller_id)) {
                        $transaction->status = $this->orderTransactionRepository::pay();
                        $timer = Carbon::make(now())->addMinutes(
                            (float)$transaction->category->pay_time
                        );
                        try {
                            DB::beginTransaction();
                            $transaction->timer = $timer;
                            $this->orderRepository->update($transaction->order,[
                                'status' => $this->orderRepository::isRequestedStatus()
                            ]);
                            $this->orderTransactionRepository->update([['id','!=',$transaction->id]],['status' => $this->orderTransactionRepository::cancel()]);
                            $sms->sends(
                                $this->createText('pay_transaction',$transaction),
                                $transaction->customer,
                                "$subject",
                                $transaction->id
                            );
                            $transaction = $this->orderTransactionRepository->save($transaction);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام تایید درخواست رخ داده است.']
                                    ]
                                ],
                                'status' => 'error'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['درخواست معاله تایید شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    }
                    else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => 'شما اجازه این کار را ندارید.'
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::pay():{
                    if ((auth()->id() == $transaction->customer_id)) {
                        $commission = $transaction->commission;
                        $intermediary = $transaction->intermediary;
                        $timer = Carbon::make(now())->addMinutes(
                            (float)$transaction->category->send_time
                        );
                        $price = $transaction->order->price + $commission/2 + $intermediary/2;
                        $forms = collect(json_decode($transaction->category->forms))->where('status','normal')
                            ->where('for','customer');

                        $old_data = json_decode($data->value);
                        if (!empty($forms) && !is_null($forms)){
                            foreach ($forms as $key => $item) {
                                if ($item->required && (!$request->has($item->name))){
                                    return response([
                                        'message' => [
                                            'transaction' => [__('validation.required', ['attribute' => strip_tags($item->label)])]
                                        ],
                                        'status' => 'error'
                                    ],Response::HTTP_UNPROCESSABLE_ENTITY);
                                }

                                if ($item->type == 'customRadio' && !in_array($request->{$item->name},collect($item->options)->map(function ($item2) {
                                        return $item2->value;
                                    })->toArray())) {
                                    return response([
                                        'message' => [
                                            'transaction' => [__('validation.in', ['attribute' => strip_tags($item->label)])]
                                        ],
                                        'status' => 'error'
                                    ],Response::HTTP_UNPROCESSABLE_ENTITY);
                                }

                                $old_data->{$item->name} = $request[$item->name];
                            }
                        }
                        $this->orderTransactionRepository->updateData($transaction,['value' => json_encode($old_data)]);
                        if ($price <= $transaction->customer->balance){
                            try {
                                DB::beginTransaction();
                                $this->orderTransactionRepository->newPayment([
                                    'orders_transactions_id' => $transaction->id,
                                    'user_id' => auth()->id(),
                                    'price' => $price,
                                    'status' => $this->orderTransactionRepository::successPayment(),
                                    'gateway' => 'wallet',
                                ]);
                                $transaction->timer = $timer;
                                $transaction->status = $this->orderTransactionRepository::send();
                                $transaction = $this->orderTransactionRepository->save($transaction);
                                DB::commit();
                            } catch (Exception $e) {
                                DB::rollBack();
                                return response([
                                    'data' => [
                                        'message' => [
                                            'transaction' => ['خطایی در هنگام تایید درخواست رخ داده است.']
                                        ]
                                    ],
                                    'status' => 'error'
                                ],Response::HTTP_INTERNAL_SERVER_ERROR);
                            }

                            $sms->sends(
                                $this->createText('send_transaction',$transaction),
                                $transaction->seller,
                                "$subject",
                                $transaction->id
                            );
                            $transaction->customer->forceWithdraw((float)$price, ['description' => $transaction->order->slug.'بابت معامله', 'from_admin'=> true]);
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['پرداخت با موفقیت انجام شد.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_OK);
                        } else {
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['موجودی حساب شما کمتر از مبلغ اگهی.']
                                    ]
                                ],
                                'status' => 'error'
                            ],Response::HTTP_NOT_ACCEPTABLE);
                        }
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::send():{
                    if ((auth()->id() == $transaction->seller_id)) {
                        $forms = collect(json_decode($transaction->category->forms))->where('status','normal')
                            ->where('for','seller');
                        $old_data = json_decode($data->value);
                        if (!empty($forms) && !is_null($forms)){
                            foreach ($forms as $key => $item) {
                                if ($item->required && (!$request->has($item->name))) {
                                    return response([
                                        'message' => [
                                            'transaction' => [__('validation.required', ['attribute' => strip_tags($item->label)])]
                                        ],
                                        'status' => 'error'
                                    ],Response::HTTP_UNPROCESSABLE_ENTITY);
                                }

                                if ($item->type == 'customRadio' && !in_array($request->{$item->name},collect($item->options)->map(function ($item2) {
                                        return $item2->value;
                                    })->toArray())) {
                                    return response([
                                        'message' => [
                                            'transaction' => [__('validation.in', ['attribute' => strip_tags($item->label)])]
                                        ],
                                        'status' => 'error'
                                    ],Response::HTTP_UNPROCESSABLE_ENTITY);
                                }

                                $old_data->{$item->name} = $request[$item->name];
                            }
                        }
                        if ($transaction->order->category->type == $this->categoryRepository::physical()) {
                            $validator = Validator::make($request->all(),[
                                'send_id' => ['required','exists:sends,id'],
                                'transfer_result' => ['required','string','max:250'],
                            ],[],[
                                'send_id' => 'روش ارسال',
                                'transfer_result' => 'کد رهگیری'
                            ]);
                            if ($validator->fails()){
                                return response([
                                    'data' => [
                                        'message' => $validator->errors()
                                    ],
                                    'status' => 'error'
                                ],Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                        }

                        $transaction->data = $this->orderTransactionRepository->updateData($transaction,[
                            'value' => json_encode($old_data),
                            'send_id' => $request['send_id'],
                            'transfer_result' => $request['transfer_result']
                        ]);
                        if ($transaction->category->control){
                            $status = $this->orderTransactionRepository::control();
                            $timer = null;
                            $sms->sends($this->createText('control_data',$transaction)
                                ,$transaction->customer,"$subject",$transaction->id);
                        } else {
                            $status = $this->orderTransactionRepository::receive();
                            $timer = null;
                            if ($transaction->category->type == $this->categoryRepository::digital())
                                $timer = Carbon::make(now())->addMinutes(
                                    (float)$transaction->category->receive_time
                                );
                            elseif ($transaction->category->type == $this->categoryRepository::physical()) {
                                if (@$transaction->order->city == @$transaction->customer->city && @$transaction->order->province == @$transaction->customer->province)
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$transaction->data->send->send_time_inner_city
                                    );
                                else
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$transaction->data->send->send_time_outer_city
                                    );
                            }
                            $sms->sends(
                                $this->createText('receive_transaction',$transaction),
                                $transaction->customer,
                                "$subject",
                                $transaction->id
                            );
                        }
                        $this->orderTransactionRepository->update([],
                            ['timer' => $timer,'status'=>$status,'received_status' => 0,'received_result' => null],
                            $transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['ارسال با موفقیت انجام شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::receive():{
                    if ((auth()->id() == $transaction->customer_id) || (auth()->id() == $transaction->seller_id && $timerStatus < 0)) {
                        if ($transaction->received_status == 2 && auth()->id() == $transaction->seller_id)
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['گزارش ایراد در اطلاعات ارسالی و یا عدم دریافت محصول توسط خریدار شده است لظفا تا اعلام نتیحه منتظر بمانید.']
                                    ]
                                ],
                                'status' => 'error'
                            ],Response::HTTP_NOT_ACCEPTABLE);

                        try {
                            DB::beginTransaction();
                            $transaction->status = $this->orderTransactionRepository::complete();
                            $transaction->received_status = 1;
                            $transaction->received_result = null;
                            $transaction->order->status = $this->orderRepository::isFinishedStatus();
                            $this->orderRepository->save($transaction->order);
                            $this->orderTransactionRepository->save($transaction);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام تایید درخواست رخ داده است.']
                                    ]
                                ],
                                'status' => 'error'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        $text = $this->createText('complete_transaction',$transaction);
                        $sms->sends($text,$transaction->seller,"$subject",$transaction->id);
                        $sms->sends($text,$transaction->customer,"$subject",$transaction->id);
                        if (auth()->id() == $transaction->seller_id){
                            $sms->sends(
                                $this->createText('skip_step',$transaction),
                                $transaction->customer,
                                "$subject",
                                $transaction->id
                            );
                        }
                        $this->sendMoney($transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['معامله با موفقیت تکمیل شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::noReceive():{
                    if ((auth()->id() == $transaction->seller_id)) {
                        $forms = collect(json_decode($transaction->category->forms))->where('status','normal')
                            ->where('for','seller');
                        $old_data = json_decode($data->value);
                        foreach ($forms as $key => $item) {
                            if ($item->required && (!$request->has($item->name))) {
                                return response([
                                    'message' => [
                                        'transaction' => [__('validation.required', ['attribute' => strip_tags($item->label)])]
                                    ],
                                    'status' => 'error'
                                ],Response::HTTP_UNPROCESSABLE_ENTITY);
                            }

                            if ($item->type == 'customRadio' && !in_array($request->{$item->name},collect($item->options)->map(function ($item2) {
                                    return $item2->value;
                                })->toArray())) {
                                return response([
                                    'message' => [
                                        'transaction' => [__('validation.in', ['attribute' => strip_tags($item->label)])]
                                    ],
                                    'status' => 'error'
                                ],Response::HTTP_UNPROCESSABLE_ENTITY);
                            }

                            $old_data->{$item->name} = $request[$item->name];
                        }
                        if ($transaction->order->category->type == $this->categoryRepository::physical()) {
                            $validator = Validator::make($request->all(),[
                                'send_id' => ['required','exists:sends,id'],
                                'transfer_result' => ['required','string','max:250'],
                            ],[],[
                                'send_id' => 'روش ارسال',
                                'transfer_result' => 'کد رهگیری'
                            ]);
                            if ($validator->fails()){
                                return response([
                                    'data' => [
                                        'message' => $validator->errors()
                                    ],
                                    'status' => 'error'
                                ],Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                        }
                        $this->orderTransactionRepository->updateData($transaction,[
                            'value' => json_encode($old_data),
                            'send_id' => $request['send_id'],
                            'transfer_result' => $request['transfer_result']
                        ]);
                        if ($transaction->category->control){
                            $status = $this->orderTransactionRepository::control();
                            $timer = null;
                            $sms->sends($this->createText('control_data',$transaction)
                                ,$transaction->customer,"$subject",$transaction->id);
                        } else {
                            $status = $this->orderTransactionRepository::receive();
                            $timer = null;
                            if ($transaction->category->type == $this->categoryRepository::digital())
                                $timer = Carbon::make(now())->addMinutes(
                                    (float)$transaction->category->receive_time
                                );
                            elseif ($transaction->category->type == $this->categoryRepository::physical()) {
                                if (@$transaction->order->city == @$transaction->customer->city && @$transaction->order->province == @$transaction->customer->province)
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$transaction->data->send->send_time_inner_city
                                    );
                                else
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$transaction->data->send->send_time_outer_city
                                    );
                            }
                            $sms->sends(
                                $this->createText('receive_transaction',$transaction),
                                $transaction->customer,
                                "$subject",
                                $transaction->id
                            );
                        }
                        $this->orderTransactionRepository->update([],
                            ['timer' => $timer,'status'=>$status,'received_status' => 0,'received_result' => null],
                            $transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['ارسال با موفقیت انجام شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::complete():{
                    if ((auth()->id() == $transaction->customer_id)) {
                        if (is_null($transaction->comment) || empty($transaction->comment)){
                            $validator = Validator::make($request->all(),[
                                'comment' => 'required|string|max:250',
                                'rate' => 'required|integer|between:0,5',
                            ],[],[
                                'comment' => 'متن نظر',
                                'rate' => 'متن نظر',
                            ]);
                            if ($validator->fails()){
                                return response([
                                    'data' =>  [
                                        'message' => $validator->errors()
                                    ], 'status' => 'error'
                                ],Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                            $comment = [
                                'user_id' => auth()->id(),
                                'status' => $this->commentRepository::newStatus(),
                                'content' => $request['comment'],
                                'commentable_id'=> $transaction->seller_id,
                                'score' => $request['rate'],
                                'order_transaction_id' => $transaction->id
                            ];
                            $this->userRepository->registerComment(Auth::user(),$comment);
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['نظر شما با موفقیت ثبت شد.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_OK);
                        } else {
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['برای این معامله فبلا نظر ثبت شده است.']
                                    ]
                                ],
                                'status' => 'error'
                            ],Response::HTTP_NOT_ACCEPTABLE);
                        }
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
            }
        } elseif ($transaction->is_returned) {
            switch ($transaction->status)
            {
                case $this->orderTransactionRepository::sendingData():{
                    if ((auth()->id() == $transaction->seller_id)) {
                        $forms = collect(json_decode($transaction->category->forms))->where('status','return')
                            ->where('for','seller');
                        $old_data = json_decode($data->value);
                        if (!empty($forms) && !is_null($forms)){
                            foreach ($forms as $key => $item) {
                                if ($item->required && (!$request->has($item->name)))
                                    return response([
                                        'data' => [],
                                        'message' => [
                                            'transaction' => [__('validation.required', ['attribute' => strip_tags($item->label)])]
                                        ],
                                        'status' => 'error'
                                    ],Response::HTTP_UNPROCESSABLE_ENTITY);

                                if ($item->type == 'customRadio' && !in_array($request->{$item->name},collect($item->options)->map(function ($item2) {
                                        return $item2->value;
                                    })->toArray())) {
                                    return response([
                                        'message' => [
                                            'transaction' => [__('validation.in', ['attribute' => strip_tags($item->label)])]
                                        ],
                                        'status' => 'error'
                                    ],Response::HTTP_UNPROCESSABLE_ENTITY);
                                }
                                $old_data->{$item->name} = $request[$item->name];
                            }
                        }
                        $this->orderTransactionRepository->updateData($transaction,[
                            'value' => json_encode($old_data),
                        ]);
                        $timer = Carbon::make(now())->addMinutes(
                            (float)$transaction->category->send_time
                        );
                        $status = $this->orderTransactionRepository::send();
                        $this->orderTransactionRepository->update([],['status' => $status,'timer' => $timer],$transaction);
                        $text = $this->createText('returned_send_transaction',$transaction);
                        $model = $transaction->customer;
                        $sms->sends($text,$model,"$subject",$transaction->id);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['اطلاعات با موفقیت ارسال شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::send():{
                    if ((auth()->id() == $transaction->customer_id)) {
                        $forms = collect(json_decode($transaction->category->forms))->where('status','return')
                            ->where('for','customer');
                        $old_data = json_decode($data->value);
                        if (!empty($forms) && !is_null($forms)){
                            foreach ($forms as $key => $item) {
                                if ($item['required'] && !$request->has($item['name']))
                                    return response([
                                        'data' => [],
                                        'message' => [
                                            'transaction' => [__('validation.required', ['attribute' => strip_tags($item['label'])])]
                                        ],
                                        'status' => 'error'
                                    ],Response::HTTP_UNPROCESSABLE_ENTITY);
                                $old_data[$item['name']] = $request[$item['name']];
                            }
                        }
                        if ($transaction->order->category->type == $this->categoryRepository::physical()) {
                            $validator = Validator::make($request->all(),[
                                'send_id' => ['required','exists:sends,id'],
                                'transfer_result' => ['required','string','max:250'],
                            ],[],[
                                'send_id' => 'روش ارسال',
                                'transfer_result' => 'کد رهگیری'
                            ]);
                            if ($validator->fails()){
                                return response([
                                    'data' => [
                                        'message' => $validator->errors()
                                    ],
                                    'status' => 'error'
                                ],Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                        }
                        $transaction->data = $this->orderTransactionRepository->updateData($transaction,[
                            'value' => json_encode($old_data),
                        ]);
                        $timer = null;
                        if ($transaction->category->control){
                            $status = $this->orderTransactionRepository::control();
                            $sms->sends($this->createText('control_data',$transaction)
                                ,$transaction->seller,"$subject",$transaction->id);
                        } else {
                            $status = $this->orderTransactionRepository::receive();
                            if ($transaction->category->type == $this->categoryRepository::digital())
                                $timer = Carbon::make(now())->addMinutes(
                                    (float)$transaction->category->receive_time
                                );
                            elseif ($transaction->order->category->type == $this->categoryRepository::physical()) {
                                if (@$transaction->order->city == @$transaction->customer->city && @$transaction->order->province == @$transaction->customer->province)
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$transaction->data->send->send_time_inner_city
                                    );
                                else
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$transaction->data->send->send_time_outer_city
                                    );
                            }
                            $this->orderTransactionRepository->update([],
                                ['timer' => $timer,'status'=>$status,'received_status' => 0,'received_result' => null],
                                $transaction);
                            $sms->sends(
                                $this->createText('returned_receive_transaction',$transaction),
                                $transaction->seller,
                                "$subject",
                                $transaction->id
                            );
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['ارسال با موفقیت انجام شد.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_OK);
                        }
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                }
                case $this->orderTransactionRepository::receive():{
                    if ((auth()->id() == $transaction->seller_id) || (auth()->id() == $transaction->customer_id && $timerStatus < 0)) {
                        if ($transaction->received_status == 2 && auth()->id() == $transaction->customer_id)
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['گزارش ایراد در اطلاعات ارسالی و یا عدم دریافت محصول توسط فروشنده شده است لظفا تا اعلام نتیحه منتظر بمانید.']
                                    ]
                                ],
                                'status' => 'error'
                            ],Response::HTTP_NOT_ACCEPTABLE);
                        $transaction->status = $this->orderTransactionRepository::cancel();
                        $transaction->timer = null;
                        $transaction->received_status = 1;
                        $transaction->received_result = null;
                        $text = $this->createText('cancel_transaction',$transaction);
                        $transaction->order->status = $this->orderRepository::isConfirmedStatus();
                        try {
                            DB::beginTransaction();
                            $this->orderRepository->save($transaction->order);
                            $this->orderTransactionRepository->save($transaction);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام تایید درخواست رخ داده است.']
                                    ]
                                ],
                                'status' => 'error'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        $this->backMoney($transaction);
                        $sms->sends($text, $transaction->seller, "$subject", $transaction->id);
                        $sms->sends($text, $transaction->customer, "$subject", $transaction->id);
                        if (auth()->id() == $transaction->customer_id){
                            $sms->sends(
                                $this->createText('skip_step',$transaction),
                                $transaction->seller,
                                "$subject",
                                $transaction->id
                            );
                        }
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['تایید دریافت و لغو معامله با موفقیت انجام شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::noReceive():{
                    if ((auth()->id() == $transaction->customer_id)) {
                        $forms = collect(json_decode($transaction->category->forms))->where('status','return')
                            ->where('for','customer');
                        $old_data = json_decode($data->value);
                        if (!empty($forms) && !is_null($forms)){
                            foreach ($forms as $key => $item) {
                                if ($item['required'] && !$request->has($item['name']))
                                    return response([
                                        'data' => [],
                                        'message' => [
                                            'transaction' => [__('validation.required', ['attribute' => strip_tags($item['label'])])]
                                        ],
                                        'status' => 'error'
                                    ],Response::HTTP_UNPROCESSABLE_ENTITY);
                                $old_data[$item['name']] = $request[$item['name']];
                            }
                        }
                        if ($transaction->order->category->type == $this->categoryRepository::physical()) {
                            $validator = Validator::make($request->all(),[
                                'send_id' => ['required','exists:sends,id'],
                                'transfer_result' => ['required','string','max:250'],
                            ],[],[
                                'send_id' => 'روش ارسال',
                                'transfer_result' => 'کد رهگیری'
                            ]);
                            if ($validator->fails()){
                                return response([
                                    'data' => [
                                        'message' => $validator->errors()
                                    ],
                                    'status' => 'error'
                                ],Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                        }
                        $transaction->data = $this->orderTransactionRepository->updateData($transaction,[
                            'value' => json_encode($old_data),
                        ]);
                        $timer = null;
                        if ($transaction->category->control){
                            $status = $this->orderTransactionRepository::control();
                            $timer = null;
                            $sms->sends($this->createText('control_data',$transaction)
                                ,$transaction->seller,"$subject",$transaction->id);
                        } else {
                            $status = $this->orderTransactionRepository::receive();
                            if ($transaction->category->type == $this->categoryRepository::digital())
                                $timer = Carbon::make(now())->addMinutes(
                                    (float)$transaction->category->receive_time
                                );
                            elseif ($transaction->order->category->type == $this->categoryRepository::physical()) {
                                if (@$transaction->order->city == @$transaction->customer->city && @$transaction->order->province == @$transaction->customer->province)
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$transaction->data->send->send_time_inner_city
                                    );
                                else
                                    $timer = Carbon::make(now())->addMinutes(
                                        (float)$transaction->data->send->send_time_outer_city
                                    );
                            }
                            $this->orderTransactionRepository->update([],
                                ['timer' => $timer,'status'=>$status,'received_status' => 0,'received_result' => null],
                                $transaction);
                            $sms->sends(
                                $this->createText('returned_receive_transaction',$transaction),
                                $transaction->customer,
                                "$subject",
                                $transaction->id
                            );
                        }
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['ارسال با موفقیت انجام شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'error'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
            }
        }
    }

    public function cancel($id)
    {
        $sms = new SendMessages();
        $transaction = $this->orderTransactionRepository->getMyTransaction(Auth::id(),$id);
        $timerStatus = Carbon::make(now())->diff($transaction->timer)->format('%r%s');
        $cancel = $this->orderTransactionRepository::cancel();
        $complete = $this->orderTransactionRepository::complete();
        $orderStatus = $this->orderRepository::isConfirmedStatus();
        $orderStatusFinished = $this->orderRepository::isFinishedStatus();
        if (!$transaction->is_returned) {
            $texts = $this->createText('cancel_transaction',$transaction);
            switch ($transaction->status){
                case $this->orderTransactionRepository::confirm():{
                    $transaction->status = $cancel;
                    $transaction->order->status = $orderStatus;
                    try {
                        DB::beginTransaction();
                        $this->orderTransactionRepository->save($transaction);
                        $this->orderTransactionRepository->updateData($transaction,['value' => null]);
                        $this->orderRepository->save($transaction->order);
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['خطایی در هنگام لغو معامله رخ داده است.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    $sms->sends($texts,$transaction->seller);
                    $sms->sends($texts,$transaction->customer);
                    $this->backMoney($transaction);
                    return response([
                        'data' => [
                            'message' => [
                                'transaction' => ['معامله با موفقیت لغو شد.']
                            ]
                        ],
                        'status' => 'success'
                    ],Response::HTTP_OK);
                    break;
                }
                case $this->orderTransactionRepository::pay():{
                    if ((auth()->id() == $transaction->customer_id) || (auth()->id() == $transaction->seller_id && $timerStatus < 0)) {
                        $transaction->status = $cancel;
                        $transaction->order->status = $orderStatus;
                        try {
                            DB::beginTransaction();
                            $this->orderTransactionRepository->save($transaction);
                            $this->orderTransactionRepository->updateData($transaction,['value' => null]);
                            $this->orderRepository->save($transaction->order);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام لغو معامله رخ داده است.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        $sms->sends($texts,$transaction->seller);
                        $sms->sends($texts,$transaction->customer);
                        $this->backMoney($transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['معامله با موفقیت لغو شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::send():{
                    if ((auth()->id() == $transaction->seller_id) || (auth()->id() == $transaction->customer_id && $timerStatus < 0)) {
                        $transaction->status = $cancel;
                        $transaction->received_status = 0;
                        $transaction->order->status = $orderStatus;
                        try {
                            DB::beginTransaction();
                            $this->orderTransactionRepository->save($transaction);
                            $this->orderTransactionRepository->updateData($transaction,['value' => null]);
                            $this->orderRepository->save($transaction->order);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام لغو معامله رخ داده است.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        $sms->sends($texts,$transaction->seller);
                        $sms->sends($texts,$transaction->customer);
                        $this->backMoney($transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['معامله با موفقیت لغو شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::noReceive():{
                    if (auth()->id() == $transaction->seller_id || (auth()->id() == $transaction->customer_id && $timerStatus < 0)) {
                        $transaction->status = $cancel;
                        $transaction->received_status = 0;
                        $transaction->order->status = $orderStatus;
                        try {
                            DB::beginTransaction();
                            $this->orderTransactionRepository->save($transaction);
                            $this->orderTransactionRepository->updateData($transaction,['value' => null]);
                            $this->orderRepository->save($transaction->order);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام لغو معامله رخ داده است.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        $sms->sends($texts,$transaction->seller);
                        $sms->sends($texts,$transaction->customer);
                        $this->backMoney($transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['معامله با موفقیت لغو شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
            }
        } elseif ($transaction->is_returned) {
            $texts = $this->createText('complete_transaction',$transaction);
            switch ($transaction->status){
                case $this->orderTransactionRepository::sendingData():{
                    if ((auth()->id() == $transaction->customer_id && $timerStatus < 0)) {
                        $transaction->status = $cancel;
                        $transaction->order->status = $orderStatus;
                        try {
                            DB::beginTransaction();
                            $this->orderTransactionRepository->save($transaction);
                            $this->orderTransactionRepository->updateData($transaction,['value' => null]);
                            $this->orderRepository->save($transaction->order);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام لفو معامله رخ داده است.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        $sms->sends($texts,$transaction->seller);
                        $sms->sends($texts,$transaction->customer);
                        $this->backMoney($transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['معامله با موفقیت لفو شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::send():{
                    if ((auth()->id() == $transaction->seller_id && $timerStatus < 0)) {
                        $transaction->status = $complete;
                        $transaction->received_status = 0;
                        $transaction->is_returned = 0;
                        $transaction->order->status = $orderStatusFinished;
                        try {
                            DB::beginTransaction();
                            $this->orderTransactionRepository->save($transaction);
                            $this->orderTransactionRepository->updateData($transaction,['value' => null]);
                            $this->orderRepository->save($transaction->order);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام تکمیل معامله رخ داده است.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        $sms->sends($texts,$transaction->seller);
                        $sms->sends($texts,$transaction->customer);
                        $this->sendMoney($transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['معامله با موفقیت تکمیل شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
                case $this->orderTransactionRepository::noReceive():{
                    if (auth()->id() == $transaction->seller_id && $timerStatus < 0) {
                        $transaction->status = $complete;
                        $transaction->received_status = 0;
                        $transaction->is_returned = 0;
                        $transaction->order->status = $orderStatusFinished;
                        try {
                            DB::beginTransaction();
                            $this->orderTransactionRepository->save($transaction);
                            $this->orderTransactionRepository->updateData($transaction,['value' => null]);
                            $this->orderRepository->save($transaction->order);
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            return response([
                                'data' => [
                                    'message' => [
                                        'transaction' => ['خطایی در هنگام تکمیل معامله رخ داده است.']
                                    ]
                                ],
                                'status' => 'success'
                            ],Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                        $sms->sends($texts,$transaction->seller);
                        $sms->sends($texts,$transaction->customer);
                        $this->sendMoney($transaction);
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['معامله با موفقیت تکمیل شد.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_OK);
                    } else {
                        return response([
                            'data' => [
                                'message' => [
                                    'transaction' => ['شما اجازه این کار را ندارید.']
                                ]
                            ],
                            'status' => 'success'
                        ],Response::HTTP_FORBIDDEN);
                    }
                    break;
                }
            }
        }
    }

    public function no_receive($id , Request $request)
    {
        $transaction = $this->orderTransactionRepository->getMyTransaction(Auth::id(),$id);
        if ($transaction->status == $this->orderTransactionRepository::receive())
        {
            $validator = Validator::make($request->all(),[
                'received_result' => 'required|string|max:250',
            ],[],[
                'received_result' => 'توضیحات',
            ]);
            if ($validator->fails()){
                return response([
                    'data' =>  [
                        'message' => $validator->errors()
                    ], 'status' => 'error'
                ],Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $this->orderTransactionRepository->update([['status',$this->orderTransactionRepository::receive()]],[
                'received_result' => $request['received_result'],
                'received_status' => 2,
            ],$transaction);
            $sms = new SendMessages();
            if (auth()->id() == $transaction->seller_id){
                $sms->sends(
                    $this->createText('return_no_receive_transaction',$transaction),
                    $transaction->customer,
                    $this->notificationRepository->transactionStatus(),
                    $transaction->id
                );
            } else {
                $sms->sends(
                    $this->createText('no_receive_transaction',$transaction),
                    $transaction->seller,
                    $this->notificationRepository->transactionStatus(),
                    $transaction->id
                );
            }
            return response([
                'data' =>  [
                    'message' => [
                        'refund' => ['درخواست شما با موفقیت ثبت شد و در حال بررسی قرار گرفت.']
                    ]
                ], 'status' => 'success'
            ],Response::HTTP_OK);
        }
        return response([
            'data' =>  [
                'message' => [
                    'refund' => ['درخواست شما در این وضعیت مجاز نمی باشد.']
                ]
            ], 'status' => 'error'
        ],Response::HTTP_NOT_ACCEPTABLE);
    }

    public function requestToReturn($id , Request $request)
    {
        $transaction = $this->orderTransactionRepository->getMyTransaction(Auth::id(),$id);
        if ($transaction->status == $this->orderTransactionRepository::receive() && !$transaction->is_returned)
        {
            $validator = Validator::make($request->all(),[
                'refunded_cause' => 'required|string|max:250',
                'refunded_images' => 'array|max:4|min:1|required',
                'refunded_images.*' => 'required|mimes:png,jpg,jpeg,mp4|max:2048',
            ],[],[
                'refunded_cause' => 'علت مرجوعیت',
                'refunded_images' => 'اسناد',
                'refunded_images.*' => 'سند',
            ]);
            if ($validator->fails()){
                return response([
                    'data' =>  [
                        'message' => $validator->errors()
                    ], 'status' => 'error'
                ],Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $sms = new SendMessages();
            $imagePath = "app/public/transactions";
            $gallery = [];
            if (!empty($request->file('refunded_images'))){
                $file = $request->file('refunded_images');
                foreach ($file as $item)
                {
                    $fileName = $item->getClientOriginalName();
                    $fileName = Carbon::now()->timestamp.'-'.$fileName;
                    $item->move(storage_path($imagePath),$fileName);
                    $files = "storage/transactions/{$fileName}";
                    $gallery[] = $files;
                }
            }
            $this->orderTransactionRepository->update([['status',$this->orderTransactionRepository::receive()]],[
                'return_cause' => $request['refunded_cause'],
                'return_images' => implode(',',$gallery),
                'status' => $this->orderTransactionRepository::isReturned(),
            ],$transaction);
            $sms->sends(
                $this->createText('request_to_return_transaction',$transaction),
                $transaction->seller,
                $this->notificationRepository->transactionStatus(),
                $transaction->id
            );
            return response([
                'data' =>  [
                    'message' => [
                        'refund' => ['درخواست مرجوعیت با موفقیت ثبت شد و در حال بررسی قرار گرفت.']
                    ]
                ], 'status' => 'success'
            ],Response::HTTP_OK);
        }
        return response([
            'data' =>  [
                'message' => [
                    'refund' => ['درخواست مرجوعیت در این وضعیت مجاز نمی باشد.']
                ]
            ], 'status' => 'error'
        ],Response::HTTP_NOT_ACCEPTABLE);
    }

    private function backMoney($transaction)
    {
        if (!empty($transaction->payment) && $transaction->payment->status == $this->orderTransactionRepository::successPayment()){
            $price = $transaction->payment->price;
            $transaction->customer->deposit($price,
                ['description' => $transaction->code.'بازگشت هزینه بابت معامله به کد ', 'from_admin'=> true]);
        }
    }

    private function sendMoney($transaction)
    {
        if (!empty($transaction->payment) && $transaction->payment->status == $this->orderTransactionRepository::successPayment()){
            $price = $transaction->payment->price;
            $commission = $transaction->commission;
            $intermediary = $transaction->intermediary;
            $final_price = ($price - ($commission) - ($intermediary));
            $transaction->seller->deposit($final_price,
                ['description' => $transaction->code.'واریز هزینه بابت معامله به کد ', 'from_admin'=> true]);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Group;
use App\Http\Resources\v1\GroupCollection;
use App\Http\Resources\v1\Panel\Chat;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\Admin\ChatList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    use ChatList;
    private $chatRepository , $userRepository;
    public function __construct(
        ChatRepositoryInterface $chatRepository , UserRepositoryInterface $userRepository
    )
    {
        $this->chatRepository = $chatRepository;
        $this->userRepository = $userRepository;
    }

   public function list()
   {
       return response([
           'data' => [
               'groups' => [
                   'records' => new GroupCollection($this->chatRepository->get($this->chatRepository->contacts()))
               ],
           ],
           'status' => 'success'
       ],Response::HTTP_OK);
   }

   public function open($group_id)
   {
       $group = $this->chatRepository->findContact($group_id);
       $this->chatRepository->seen($group);
       return response([
           'data' => [
               'group' => [
                   'record' => new Group($group)
               ],
           ],
           'status' => 'success'
       ],Response::HTTP_OK);
   }

   public function send(Request $request , $group_id)
   {
       $rateKey = 'verify-attempt:' . auth('api')->id() . '|' . request()->ip();
       if (RateLimiter::tooManyAttempts($rateKey, 150)) {
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
       $group = $this->chatRepository->findContact($group_id);
       if ($this->chatRepository::isOpen($group)){
           $validator = Validator::make($request->all(),[
               'message' => ['required','string','max:150'],
           ],[],[
               'message' => 'متن پیام',
           ]);
           if ($validator->fails()){
               return response([
                   'data' =>  [
                       'message' => $validator->errors()
                   ],
                   'status' => 'error'
               ],Response::HTTP_UNPROCESSABLE_ENTITY);
           }
           $this->chatText = $request['message'];
           $this->chatUserId = $group->user1 == auth()->id() ? $group->user2 : $group->user1;
           $chat = $this->sendChatText($this->chatRepository,$this->userRepository);
           return response([
               'data' => [
                   'chat' => [
                       'record' => new Chat($chat)
                   ],
                   'message' => [
                       'chat' => ['پیام با موفقیت ارسال شد.']
                   ],
               ],
               'status' => 'success'
           ],Response::HTTP_OK);

       } else {
           return response([
               'data' => [
                   'message' => [
                       'group' => ['این گفتوگو توسط مدیریت بسته شده است.']
                   ],
               ],
               'status' => 'error'
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

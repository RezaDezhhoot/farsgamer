<?php

namespace App\Http\Livewire\Admin\Users;
use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\OvertimeRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Sends\SendMessages;
use App\Traits\Admin\ChatList;
use App\Traits\Admin\TextBuilder;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class StoreUser extends BaseComponent
{
    use  TextBuilder , ChatList;
    public $user , $mode , $header ,$data = [] , $userRole = [] , $pass_word  , $code_id ,$score , $description , $profile_image , $auth_image;
    public $full_name  , $user_name , $phone , $province , $city  , $status , $email , $actionWallet , $editWallet , $sendMessage , $subjectMessage,
    $statusMessage , $result , $walletMessage , $banDescription , $banTime , $ban , $cards  = [] , $userWallet;
    // schedules
    public $saturday , $sunday , $monday , $tuesday , $wednesday, $thursday, $friday;
    // overtimes
    public $start_at , $end_at , $overtimes = [];

    public function mount(
        UserRepositoryInterface $userRepository , RoleRepositoryInterface $roleRepository ,
        ChatRepositoryInterface $chatRepository , NotificationRepositoryInterface $notificationRepository , $action , $id = null)
    {
        $this->authorizing('show_users');
        if ($action == 'edit')
        {
            $this->header = 'کاربر شماره '.$id;
            $this->user = $userRepository->find($id);
            $this->full_name = $this->user->name;
            $this->user_name = $this->user->user_name;
            $this->phone = $this->user->phone;
            $this->province = $this->user->province;
            $this->city = $this->user->city;
            $this->status = $this->user->status;
            $this->email = $this->user->email;
            $this->code_id = $this->user->code_id;
            $this->score = $this->user->score;
            $this->description = $this->user->description;
            $this->profile_image = $this->user->profile_image;
            $this->auth_image = $this->user->auth_image;
            $this->userRole = $this->user->roles()->pluck('name','id')->toArray();;
            $this->result = $this->user->results;
            $this->userWallet = $userRepository->walletTransactions($this->user);
            $this->result = $this->user->alerts;
            $this->chatUserId = $this->user->id;

            $this->saturday = $this->user->schedule->saturday ?? null;
            $this->sunday = $this->user->schedule->sunday ?? null;
            $this->monday = $this->user->schedule->monday ?? null;
            $this->tuesday = $this->user->schedule->tuesday ?? null;
            $this->wednesday = $this->user->schedule->wednesday ?? null;
            $this->thursday = $this->user->schedule->thursday ?? null;
            $this->friday = $this->user->schedule->friday ?? null;

            $this->chats = $chatRepository->singleContact($this->user->id);
        } elseif($action == 'create')
            $this->header = 'کاربر جدید';
        else abort(404);

        $this->mode = $action;
        $this->data['status'] = $userRepository->getStatus();
        $this->data['role'] = $roleRepository->whereNotIn('name', ['administrator', 'super_admin']);
        $this->data['action'] = [
            'deposit' => 'واریز',
            'withdraw' => 'برداشت',
        ];
        $this->data['subjectMessage'] = $notificationRepository->getSubjects();
    }

    public function store(
        SettingRepositoryInterface $settingRepository , ScheduleRepositoryInterface $scheduleRepository ,
        UserRepositoryInterface $userRepository ,NotificationRepositoryInterface $notificationRepository
    )
    {
        $this->authorizing('edit_users');
        if ($this->mode == 'edit')
            $this->saveInDataBase($scheduleRepository,$notificationRepository,$settingRepository , $userRepository,$this->user);
        else {
            $this->saveInDataBase($scheduleRepository,$notificationRepository,$settingRepository , $userRepository,$userRepository->newUserObject());
            $this->reset([
                'full_name','user_name','pass_word','phone','province',
                'city','status','email','userRole','code_id','score','description','auth_image','profile_image',
            ]);
        }
    }

    public function saveInDataBase($scheduleRepository,$notificationRepository,$settingRepository, $userRepository, $model)
    {
        $fields = [
            'full_name' => ['required', 'string','max:65'],
            'user_name' => ['required', 'string','max:255' , 'unique:users,user_name,'. ($this->user->id ?? 0)],
            'phone' => ['required', 'size:11' , 'unique:users,phone,'. ($this->user->id ?? 0)],
            'province' => ['nullable','string','in:'.implode(',',array_keys($this->data['province']))],
            'city' => ['nullable','string','in:'.implode(',',array_keys($this->data['city']))],
            'status' => ['required','in:'.implode(',',array_keys($userRepository->getStatus()))],
            'email' => ['required','email','max:200','unique:users,email,'. ($this->user->id ?? 0)],
            'code_id' => ['nullable','size:10'],
            'score' => ['required','numeric','between:0,5'],
            'description' => ['nullable','string','max:255'],
            'auth_image' => ['nullable','string','max:255'],
            'profile_image' => ['nullable','string','max:255'],
        ];
        $messages = [
            'full_name' => 'نام ',
            'user_name' => 'نام کربری',
            'phone' => 'شماره همراه',
            'province' => 'استان',
            'city' => 'شهر',
            'status' => 'وضعیت',
            'email' => 'ایمیل',
            'code_id' => 'شماره ملی',
            'score' => 'امتیاز',
            'description' => 'بیوگرافی',
            'auth_image' => 'تصویر برای احراز هویت',
            'profile_image' => 'تصویر پروفایل',
        ];

        if ($this->mode == 'create' && isset($this->pass_word))
        {
            $fields['pass_word'] = ['required','min:'.$settingRepository->getSiteFaq('password_length'),'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/'];
            $messages['pass_word'] = 'گذرواژه';
        }

        if (isset($this->actionWallet))
        {
            $fields['editWallet'] = ['required','numeric','between:0,9999999999.99'];
            $messages['editWallet'] = 'عملیات';
        }

        $this->validate($fields,[],$messages);
        if ($this->mode == 'edit' && $this->status <> $this->user->status)
            $this->notify($notificationRepository,$userRepository);

        $model->name = $this->full_name;
        $model->user_name = $this->user_name;
        $model->phone = $this->phone;
        $model->province = $this->province;
        $model->city = $this->city;
        $model->status = $this->status;
        $model->email = $this->email;
        $model->code_id = $this->code_id;
        $model->score = $this->score;
        $model->description = $this->description;
        $model->auth_image = $this->auth_image;
        $model->profile_image = $this->profile_image;


        if ($this->mode == 'create' && isset($this->pass_word))
            $model->password = Hash::make($this->pass_word);

        $userRepository->save($model);

        $scheduleRepository::updateOrCreate(['user_id'=>$model->id],[
            'saturday' => $this->saturday,'sunday' => $this->sunday,'monday' => $this->monday,'tuesday' => $this->tuesday,
            'wednesday' => $this->wednesday,'thursday' => $this->thursday,'friday'=> $this->friday
        ]);

        if ($userRepository->hasRole('super_admin'))
            $userRepository->syncRoles($model,$this->userRole);

        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function wallet(UserRepositoryInterface $userRepository)
    {
        $this->authorizing('edit_users');
        if ($this->mode == 'edit')
        {
            $this->validate([
                'actionWallet' => ['required' ,'in:deposit,withdraw'],
                'editWallet' => ['required','numeric','between:0,999999999.9999'],
                'walletMessage' => ['nullable','string','max:150'],
            ] , [] ,[
                'actionWallet' => 'عملیات',
                'editWallet' => 'مبلغ',
                'walletMessage' => 'متن پیام',
            ]);
            if ($this->actionWallet == 'deposit') {
                $this->user->deposit($this->editWallet, ['description' => $this->walletMessage, 'from_admin'=> true]);
            } else {
                try {
                    $this->user->forceWithdraw($this->editWallet, ['description' => $this->walletMessage, 'from_admin'=> true]);
                } catch (BalanceIsEmpty | InsufficientFunds $exception) {
                    $this->addError('walletAmount', $exception->getMessage());
                }
            }
            $this->userWallet = $userRepository->walletTransactions($this->user);
            $this->reset(['actionWallet', 'editWallet', 'walletMessage']);
            $this->emitNotify('کیف پول کاربر با موفقیت ویرایش شد');
        }
    }

    public function sendMessage(NotificationRepositoryInterface $notificationRepository)
    {
       if ($this->mode == 'edit')
       {
           $this->validate([
               'sendMessage' => ['required' ,' string','max:255'],
               'subjectMessage' => ['string','required','in:'.implode(',',array_keys($notificationRepository->getSubjects()))],
           ] , [] ,[
               'sendMessage' => 'متن پیام',
               'subjectMessage' => 'موضوع',
           ]);
           $notification = [
               'subject' => $this->subjectMessage,
               'content' =>  $this->sendMessage,
               'type' => $notificationRepository->privateType(),
               'user_id' => $this->user->id,
               'model' => $this->subjectMessage,
               'model_id' => $this->user->id
           ];
           $notification = $notificationRepository->create($notification);
           $this->result->push($notification);
           $this->reset(['sendMessage','subjectMessage']);
           $this->emitNotify('اطلاعات با موفقیت ثبت شد');
       }
    }

    public function render(UserRepositoryInterface $userRepository , SettingRepositoryInterface $settingRepository)
    {
        if ($this->mode == 'edit') {
            $ban = Carbon::make(now())->diff($this->user->ban,false)->format('%r%i');
            $this->ban =  $ban > 0 ? "بسته شده برای $ban دقیقه" : 'ازاد';
            $this->cards = $userRepository->getUserCards($this->user);
            $this->overtimes = $userRepository->getUserOvertimes($this->user);
        }
        $this->data['province'] = [];
        $this->data['city'] = [];
        if (isset($this->province)){
            $this->data['province'] = $settingRepository::getProvince();
            $this->data['city'] = $settingRepository->getCity($this->province) ?? [];
        }

        return view('livewire.admin.users.store-user')->extends('livewire.admin.layouts.admin');;
    }

    public function notify(NotificationRepositoryInterface $notificationRepository , UserRepositoryInterface $userRepository)
    {
        $text = [];
        switch ($this->status){
            case $userRepository::notConfirmedStatus():{
                $text = $this->createText('not_confirmed',$this->user);
                break;
            }
            case $userRepository::confirmedStatus():{
                $text = $this->createText('auth',$this->user);
                break;
            }
        }
        $send = new SendMessages();
        $send->sends($text,$this->user,$notificationRepository->userStatus(),$this->user->id);
    }

    public function setBan(NotificationRepositoryInterface $notificationRepository , UserRepositoryInterface $userRepository)
    {
        $this->authorizing('edit_users');
        $this->validate([
            'banTime' => ['required','integer','between:0,9999999999'],
            'banDescription' => ['nullable','string','max:250'],
        ],[],[
            'banTime' => 'ساعت',
            'banDescription' => 'علت',
        ]);
        $this->user->ban = Carbon::make(now())->addHours($this->banTime);
        $userRepository->save($this->user);
        $notification = [
            'subject' => $notificationRepository->authStatus(),
            'content' =>  $this->banDescription,
            'type' => $notificationRepository->privateType(),
            'user_id' => $this->user->id,
            'model' => $notificationRepository->authStatus(),
            'model_id' => $this->user->id
        ];
        $notification = $notificationRepository->create($notification);
        $this->result->push($notification);
        $this->reset(['banTime','banDescription']);
        $sends = new SendMessages();
        $sends->sends($this->createText('baned_user',$this->user),$this->user,$notificationRepository->authStatus(),$this->user->id);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function confirmCard(CardRepositoryInterface $cardRepository,$id)
    {
        $this->authorizing('edit_cards');
        $card = $cardRepository->find($id,false);
        $card->status = $cardRepository::confirmStatus();
        $cardRepository->save($card);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteCard(CardRepositoryInterface $cardRepository,$id)
    {
        $this->authorizing('delete_cards');
        $card = $cardRepository->find($id,false);
        $cardRepository->delete($card);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteOverTimer(OvertimeRepositoryInterface $overtimeRepository ,$id)
    {
        $this->authorizing('edit_users');
        $overtime = $overtimeRepository->find($id);
        $overtimeRepository->delete($overtime);
    }

    public function newOverTime(OvertimeRepositoryInterface $overtimeRepository)
    {
        $this->authorizing('edit_users');
        if ($this->mode == 'edit'){
            $this->validate([
                'start_at' => ['required','date_format:Y-m-d H:i'],
                'end_at' => ['required','date_format:Y-m-d H:i'],
            ],[],[
                'start_at' => 'تاریخ شروع',
                'end_at' => 'تاریخ پایان',
            ]);
            $overtime = [
                'user_id' => $this->user->id,
                'start_at' =>  $this->start_at,
                'end_at' => $this->end_at,
                'manger' => auth()->id(),
            ];
            $overtimeRepository->create($overtime);
            $this->reset(['start_at','end_at']);
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        }
    }

}

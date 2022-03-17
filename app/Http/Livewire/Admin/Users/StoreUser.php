<?php

namespace App\Http\Livewire\Admin\Users;
use App\Http\Livewire\BaseComponent;
use App\Models\Card;
use App\Models\Notification;
use App\Models\Overtime;
use App\Models\Schedule;
use App\Models\Setting;
use App\Sends\SendMessages;
use App\Traits\Admin\ChatList;
use App\Traits\Admin\Sends;
use App\Traits\Admin\TextBuilder;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Bavix\Wallet\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class StoreUser extends BaseComponent
{
    use AuthorizesRequests, Sends , TextBuilder , ChatList;
    public $user , $mode , $header ,$data = [] , $userRole = [] , $pass_word  , $code_id ,$score , $description , $profile_image , $auth_image;
    public $full_name  , $user_name , $phone , $province , $city  , $status , $email , $actionWallet , $editWallet , $sendMessage , $subjectMessage,
    $statusMessage , $result , $walletMessage , $banDescription , $banTime , $ban , $cards  = [] , $userWallet;
    // schedules
    public $saturday , $sunday , $monday , $tuesday , $wednesday, $thursday, $friday;
    // overtimes
    public $start_at , $end_at , $overtimes = [];

    public function mount($action , $id = null)
    {
        $this->authorize('show_users');
        if ($action == 'edit')
        {
            $this->header = 'کاربر شماره '.$id;
            $this->user = User::findOrFail($id);
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
            $this->userWallet = $this->user->walletTransactions()->where('confirmed', 1)->get();
            $this->result = $this->user->alerts;
            $this->chatUserId = $this->user->id;

            $this->saturday = $this->user->schedule->saturday ?? null;
            $this->sunday = $this->user->schedule->sunday ?? null;
            $this->monday = $this->user->schedule->monday ?? null;
            $this->tuesday = $this->user->schedule->tuesday ?? null;
            $this->wednesday = $this->user->schedule->wednesday ?? null;
            $this->thursday = $this->user->schedule->thursday ?? null;
            $this->friday = $this->user->schedule->friday ?? null;

            $this->chats = \auth()->user()->singleContact($this->user->id);
        } elseif($action == 'create')
            $this->header = 'کاربر جدید';
        else abort(404);

        $this->mode = $action;
        $this->data['status'] = User::getStatus();
        $this->data['role'] = Role::whereNotIn('name', ['administrator', 'super_admin'])->latest()->get();
        $this->data['action'] = [
            'deposit' => 'واریز',
            'withdraw' => 'برداشت',
        ];
        $this->data['subjectMessage'] = Notification::getSubject();
    }

    public function store()
    {
        $this->authorize('edit_users');
        if ($this->mode == 'edit')
            $this->saveInDataBase($this->user);
        else {
            $this->saveInDataBase(new User());
            $this->reset([
                'full_name','user_name','pass_word','phone','province',
                'city','status','email','userRole','code_id','score','description','auth_image','profile_image',
            ]);
        }
    }

    public function saveInDataBase(User $model)
    {
        $fields = [
            'full_name' => ['required', 'string','max:65'],
            'user_name' => ['required', 'string','max:255' , 'unique:users,user_name,'. ($this->user->id ?? 0)],
            'phone' => ['required', 'size:11' , 'unique:users,phone,'. ($this->user->id ?? 0)],
            'province' => ['required','string','in:'.implode(',',array_keys($this->data['province']))],
            'city' => ['required','string','in:'.implode(',',array_keys($this->data['city']))],
            'status' => ['required','in:'.implode(',',array_keys(User::getStatus()))],
            'email' => ['required','email','max:200','unique:users,email,'. ($this->user->id ?? 0)],
            'code_id' => ['required','size:10'],
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
            $fields['pass_word'] = ['required','min:'.Setting::getSingleRow('password_length'),'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/'];
            $messages['pass_word'] = 'گذرواژه';
        }

        if (isset($this->actionWallet))
        {
            $fields['editWallet'] = ['required','numeric','between:0,9999999999.99'];
            $messages['editWallet'] = 'عملیات';
        }

        $this->validate($fields,[],$messages);

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
            $model->pass_word = Hash::make($this->pass_word);

        $model->save();

        Schedule::updateOrCreate(['user_id'=>$model->id],[
            'saturday' => $this->saturday,'sunday' => $this->sunday,'monday' => $this->monday,'tuesday' => $this->tuesday,
            'wednesday' => $this->wednesday,'thursday' => $this->thursday,'friday'=> $this->friday
        ]);

        if (auth()->user()->hasRole('super_admin'))
            $model->syncRoles($this->userRole);

        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function wallet()
    {
        $this->authorize('edit_users');
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
            if ($this->actionWallet == Transaction::TYPE_DEPOSIT) {
                $this->user->deposit($this->editWallet, ['description' => $this->walletMessage, 'from_admin'=> true]);
            } else {
                try {
                    $this->user->forceWithdraw($this->editWallet, ['description' => $this->walletMessage, 'from_admin'=> true]);
                } catch (BalanceIsEmpty | InsufficientFunds $exception) {
                    $this->addError('walletAmount', $exception->getMessage());
                }
            }
            $this->userWallet = $this->user->walletTransactions()->where('confirmed', 1)->get();
            $this->reset(['actionWallet', 'editWallet', 'walletMessage']);
            $this->emitNotify('کیف پول کاربر با موفقیت ویرایش شد');
        }
    }

    public function sendMessage()
    {
        $this->authorize('edit_users');
       if ($this->mode == 'edit')
       {
           $this->validate([
               'sendMessage' => ['required' ,' string','max:255'],
               'subjectMessage' => ['string','required','in:'.implode(',',array_keys($this->data['subjectMessage']))],
           ] , [] ,[
               'sendMessage' => 'متن پیام',
               'subjectMessage' => 'موضوع',
           ]);
           $result = new Notification();
           $result->subject = $this->subjectMessage;
           $result->content = $this->sendMessage;
           $result->type = Notification::PRIVATE;
           $result->user_id = $this->user->id;
           $result->model = $this->subjectMessage;
           $result->model_id = null;
           $result->save();
           $this->result->push($result);
           $this->reset(['sendMessage','subjectMessage']);
           $this->emitNotify('اطلاعات با موفقیت ثبت شد');
       }
    }

    public function render()
    {
        if ($this->mode == 'edit') {
            $ban = Carbon::make(now())->diff($this->user->ban,false)->format('%r%i');
            $this->ban =  $ban > 0 ? "بسته شده برای $ban دقیقه" : 'ازاد';
            $this->cards = Card::where('user_id',$this->user->id)->get();
            $this->overtimes = Overtime::where('user_id',$this->user->id)->get();
        }
        $this->data['province'] = Setting::getProvince();
        $this->data['city'] = Setting::getCity()[$this->province] ?? [];

        return view('livewire.admin.users.store-user')->extends('livewire.admin.layouts.admin');;
    }


    public function setBan()
    {
        $this->authorize('edit_users');
        $this->validate([
            'banTime' => ['required','integer','between:0,9999999999'],
            'banDescription' => ['nullable','string','max:250'],
        ],[],[
            'banTime' => 'ساعت',
            'banDescription' => 'علت',
        ]);
        $this->user->ban = Carbon::make(now())->addHours($this->banTime);
        $this->user->save();
        $result = new Notification();
        $result->subject = Notification::AUTH;
        $result->content = $this->banDescription;
        $result->type = Notification::PRIVATE;
        $result->user_id = $this->user->id;
        $result->model = Notification::AUTH;
        $result->model_id = $this->user->id;
        $result->save();
        $this->result->push($result);
        $this->reset(['banTime','banDescription']);
        $sends = new SendMessages();
        $sends->sends($this->createText('baned_user',$this->user),$this->user,Notification::AUTH,$this->user->id);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function confirmCard($id)
    {
        $this->authorize('edit_cards');
        $card = Card::findOrFail($id);
        $card->status = Card::CONFIRMED;
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        return $card->save();
    }

    public function deleteCard($id)
    {
        $this->authorize('delete_cards');
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        return Card::findOrFail($id)->delete();
    }

    public function deleteOverTimer($id)
    {
        $this->authorize('edit_users');
        Overtime::findOrfail($id)->delete();
    }

    public function newOverTime()
    {
        $this->authorize('edit_users');
        if ($this->mode == 'edit'){
            $this->validate([
                'start_at' => ['required','date_format:Y-m-d H:i'],
                'end_at' => ['required','date_format:Y-m-d H:i'],
            ],[],[
                'start_at' => 'تاریخ شروع',
                'end_at' => 'تاریخ پایان',
            ]);
            $overtime = new Overtime();
            $overtime->user_id = $this->user->id;
            $overtime->start_at = $this->start_at;
            $overtime->end_at = $this->end_at;
            $overtime->manger = auth()->id();
            $overtime->save();
            $this->reset(['start_at','end_at']);
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        }
    }

}

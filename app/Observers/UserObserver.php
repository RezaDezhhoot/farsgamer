<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\User;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class UserObserver
{
    use TextBuilder;
    public $send;
    public function __construct()
    {
        $this->send = new SendMessages();
    }
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $text = [];
        $text = $this->createText('signUp',$user);
        $this->send->sends($text,$user,Notification::User,$user->id);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $text = [];
        switch ($user->status){
            case User::NOT_CONFIRMED:{
                $text = $this->createText('not_confirmed',$user);
                break;
            }
            case User::CONFIRMED:{
                $text = $this->createText('auth',$user);
                break;
            }
        }
        $this->send->sends($text,$user,Notification::User,$user->id);
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}

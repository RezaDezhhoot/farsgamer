<?php

namespace App\Http\Livewire\Site\Dashboard\Chats;
use App\Http\Livewire\BaseComponent;
use App\Models\Chat;
use App\Models\ChatGroup;
use App\Models\Setting;
use App\Models\User;
use App\Sends\SendMessages;
use App\Traits\Admin\TextBuilder;

class IndexChat extends BaseComponent
{
    use TextBuilder;
    public $user , $chatLists , $law , $chats;

    public $newMessage , $sender , $receiver;

    public function mount()
    {
        $this->law = Setting::where('name','chatLaw')->get();
        $this->user = auth()->user();
        $this->chatLists = ChatGroup::where('status',ChatGroup::OPEN)->where(function ($query){
            $query->where('user1',auth()->id())->orWhere('user2',auth()->id());
        })->orderBy('updated_at', 'desc')->get();
    }

    public function openChatList($id) {
        $this->chats = $this->chatLists[$id];
        $this->sender = auth()->id();
        $this->receiver = ($this->chats->user1 == $this->sender) ? $this->chats->user2 : $this->chats->user1;
    }

    public function send()
    {
        $this->validate([
            'newMessage' => ['required','max:250','string']
        ],[],[
            'newMessage' => 'پیام',
        ]);
        $chat = new Chat();
        $chat->sender_id = $this->sender;
        $chat->receiver_id = $this->receiver;
        $chat->content = $this->newMessage;
        $chat->receiver_id = $this->chats->id;
        $chat->save();
        $this->chats->slug = 'chat'.uniqid();
        $this->chats->save();
        $send = new SendMessages();
        $model = User::find($this->receiver);
        $text = $this->createText('new_message',$model);
        $send->sends($text,$model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function render()
    {
        return view('livewire.site.dashboard.chats.index-chat');
    }
}

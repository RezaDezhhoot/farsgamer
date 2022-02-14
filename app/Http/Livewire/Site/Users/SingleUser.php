<?php

namespace App\Http\Livewire\Site\Users;

use App\Http\Livewire\BaseComponent;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Traits\Admin\ChatList;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;

class SingleUser extends BaseComponent
{
    use ChatList;
    public $user , $orders , $comments , $tab;
    protected $queryString = ['tab'];

    public function mount($user)
    {
        $this->user = User::with(['orders'])->where([
            ['user_name',$user],
            ['status',User::CONFIRMED],
        ])->firstOrFail();
        $this->orders = $this->user->orders()->where('status',Order::IS_CONFIRMED)->get();
        $this->comments = Comment::where([
            ['type',Comment::USER],
            ['status',Comment::CONFIRMED],
            ['case_id',$this->user->id],
        ])->get();
        SEOMeta::setTitle($this->user->fullName,false);
        SEOMeta::setDescription($this->user->description);
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle($this->user->fullName);
        OpenGraph::setDescription($this->user->description);
        TwitterCard::setTitle($this->user->fullName);
        TwitterCard::setDescription($this->user->description);
        JsonLd::setTitle($this->user->fullName);
        JsonLd::setDescription($this->user->description);
        $this->chatUserId = $this->user->id;
        $this->chats = \auth()->user()->singleContact($this->user->id);
        JsonLd::addImage(Setting::getSingleRow('logo'));
    }

    public function render()
    {
        return view('livewire.site.users.single-user');
    }
}

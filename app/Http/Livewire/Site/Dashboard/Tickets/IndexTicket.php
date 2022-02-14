<?php

namespace App\Http\Livewire\Site\Dashboard\Tickets;

use App\Models\Setting;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Ticket;
use App\Models\User;
use Livewire\WithPagination;

class IndexTicket extends Component
{
    use WithPagination;
    public $paginate = 10;
    public function mount()
    {
        SEOMeta::setTitle('پشتیبانی',false);
        SEOMeta::setDescription(Setting::getSingleRow('seoDescription'));
        SEOMeta::addKeyword(explode(',',Setting::getSingleRow('seoKeyword')));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle('پشتیبانی');
        OpenGraph::setDescription(Setting::getSingleRow('seoDescription'));
        TwitterCard::setTitle('پشتیبانی');
        TwitterCard::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::setTitle('پشتیبانی');
        JsonLd::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::addImage(Setting::getSingleRow('logo'));
    }

    public function render()
    {
        $user = User::findOrFail(Auth::id());
        $tickets = $user->tickets()->where('parent_id',null)->orderBy('id','desc')->paginate($this->paginate);
        return view('livewire.site.dashboard.tickets.index-ticket',['tickets' => $tickets]);
    }
}

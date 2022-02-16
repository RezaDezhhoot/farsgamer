<?php

namespace App\Http\Livewire\Site\Dashboard\Tickets;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Livewire\WithPagination;

class IndexTicket extends BaseComponent
{
    use WithPagination;
    public $paginate = 10 , $user , $tickets;
    public function mount()
    {
        $this->user = \auth()->user();
        $this->tickets = $this->user->tickets()->where('parent_id',null)->orderBy('id','desc')->paginate($this->paginate);
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

        return view('livewire.site.dashboard.tickets.index-ticket');
    }
}

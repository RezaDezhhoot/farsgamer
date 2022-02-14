<?php

namespace App\Http\Livewire\Site\Dashboard\Others;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class IndexNotification extends BaseComponent
{
    public function mount()
    {
        SEOMeta::setTitle('اعلان ها',false);
        SEOMeta::setDescription(Setting::getSingleRow('seoDescription'));
        SEOMeta::addKeyword(explode(',',Setting::getSingleRow('seoKeyword')));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle('اعلان ها');
        OpenGraph::setDescription(Setting::getSingleRow('seoDescription'));
        TwitterCard::setTitle('اعلان ها');
        TwitterCard::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::setTitle('اعلان ها');
        JsonLd::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::addImage(Setting::getSingleRow('logo'));
    }
    public function render()
    {
        $notifications = Notification::where('user_id',Auth::id())->orderBy('id','desc');
        $notifications->update(['is_read'=>1]);
        return view('livewire.site.dashboard.others.index-notifications',['notifications'=>$notifications->paginate(10)]);
    }
}

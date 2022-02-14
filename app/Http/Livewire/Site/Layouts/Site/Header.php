<?php

namespace App\Http\Livewire\Site\Layouts\Site;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Setting;


class Header extends Component
{
    public $data = [];
    public function render(Response $request)
    {
        if (Setting::getSingleRow('status') == 0)
            abort(503);



        $this->data = [
            'logo' => Setting::getSingleRow('logo'),
            'tel' => Setting::getSingleRow('tel'),
            'notification' => [
                'public' => Notification::where('type',Notification::PUBLIC)->get(),
                'private' => Auth::user()->alerts()->where('is_read',0)->get(),
            ],
            'favorite' => \Cart::getContent(),
        ];
        return view('livewire.site.layouts.site.header');
    }
}

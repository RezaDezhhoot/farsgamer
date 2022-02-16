<?php

namespace App\Http\Livewire\Site\Layouts\Site;

use App\Http\Livewire\BaseComponent;
use App\Models\Notification;
use Illuminate\Http\Response;
use App\Models\Setting;


class Header extends BaseComponent
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
                'public' => Notification::where('type',Notification::PUBLIC)->take(10),
                'private' =>
                    auth()->check() ? auth()->user()->alerts()->where('is_read',0)->orderBy('id','desc')->take(10) : 0,
            ],
        ];
        return view('livewire.site.layouts.site.header');
    }
}

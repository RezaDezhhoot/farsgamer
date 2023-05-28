<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function siteNotification(Request $request)
    {
        $items = Notification::query()
            ->latest()->where('type',Notification::PUBLIC)
            ->paginate(\request('per_page',10));

        return \App\Http\Resources\V1\Notification::collection($items);
    }

    public function userNotification(Request $request)
    {
        $items = auth()->user()->alerts()
            ->latest()->paginate(\request('per_page',10));

        return \App\Http\Resources\V1\Notification::collection($items);
    }
}

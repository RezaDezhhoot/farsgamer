<?php


namespace App\Repositories\Classes;

use App\Models\Platform;
use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    public function getSiteLaw($name)
    {
        $law = collect(Setting::where('name',$name)->pluck('value')->toArray());
        return $law->sortBy('order');
    }

    public function getSiteFaq($name , $default = '')
    {
        return Setting::getSingleRow($name , $default);
    }
}

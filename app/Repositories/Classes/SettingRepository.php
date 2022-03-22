<?php


namespace App\Repositories\Classes;

use App\Helper\Helper;
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

    public function getSubjects($name, $default = [])
    {
        return Setting::getSingleRow($name , $default);
    }

    public function getFagList()
    {
        $fag = collect(Setting::where('name','question')->pluck('value')->toArray());
        return [
            'fag' => $fag->sortBy('order'),
            'categories' => array_unique(Helper::array_value_recursive('category',$fag->toArray()))
        ];
    }

    /**
     * @return mixed
     */
    public static function getProvince()
    {
        return Setting::getProvince();
    }

    /**
     * @param $province
     * @return mixed
     */
    public function getCity($province)
    {
        return Setting::getCity()[$province];
    }
}

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

    public function getAdminLaw($name)
    {
        return Setting::where('name',$name)->get()->toArray() ?? [];
    }

    public function getSiteFaq($name , $default = null)
    {
        return Setting::getSingleRow($name , $default);
    }

    public function getSubjects($name, $default = [])
    {
        return Setting::getSingleRow($name , $default);
    }

    public function getAdminFag($name)
    {
//        return
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

    public function getCities()
    {
        return Setting::getCity();
        // TODO: Implement getCities() method.
    }

    public static function updateOrCreate(array $key, array $value)
    {
        return Setting::updateOrCreate($key, $value);
    }

    public function find($id)
    {
        return Setting::findOrFail($id);
    }

    public function delete(Setting $setting)
    {
        return $setting->delete();
    }

    public function newSettingObject()
    {
        return new Setting();
    }

    public function save(Setting $setting)
    {
        $setting->save();
        return $setting;
    }

    public function codes()
    {
        return Setting::codes();
    }
}

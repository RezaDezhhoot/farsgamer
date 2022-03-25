<?php
namespace App\Repositories\Interfaces;
use App\Models\Setting;
use Illuminate\Http\Request;

interface SettingRepositoryInterface
{
    public function getSiteLaw($name);

    public function getSiteFaq($name , $default = '');

    public function getSubjects($name , $default = []);

    public function getFagList();

    public static function getProvince();

    public function getCity($province);

    public static function updateOrCreate(array $key , array $value);

    public function find($id);

    public function delete(Setting $setting);

    public function getAdminLaw($name);

    public function newSettingObject();

    public function save(Setting $setting);

    public function getAdminFag($name);

    public function codes();
}

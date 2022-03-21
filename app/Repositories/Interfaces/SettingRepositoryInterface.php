<?php
namespace App\Repositories\Interfaces;
use Illuminate\Http\Request;

interface SettingRepositoryInterface
{
    public function getSiteLaw($name);

    public function getSiteFaq($name , $default = '');
}

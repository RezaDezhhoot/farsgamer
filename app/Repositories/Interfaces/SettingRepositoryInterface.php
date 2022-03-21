<?php
namespace App\Repositories\Interfaces;
use Illuminate\Http\Request;

interface SettingRepositoryInterface
{
    public function getSiteLaw($name);

    public function getSiteFaq($name , $default = '');

    public function getSubjects($name , $default = []);

    public function getFagList();
}

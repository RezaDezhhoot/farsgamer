<?php


namespace App\Repositories\Classes;

use App\Models\Schedule;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;


class ScheduleRepository implements ScheduleRepositoryInterface
{
    public static function updateOrCreate(array $key, array $value)
    {
        return Schedule::updateOrCreate($key,$value);
    }
}

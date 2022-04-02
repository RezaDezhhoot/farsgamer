<?php

namespace App\Repositories\Interfaces;

interface ScheduleRepositoryInterface
{
    public static function updateOrCreate(array $key , array $value);
}

<?php


namespace App\Repositories\Classes;

use App\Models\Platform;
use App\Repositories\Interfaces\PlatformRepositoryInterface;
use Illuminate\Http\Request;

class PlatformRepository implements PlatformRepositoryInterface
{
    public function getAll()
    {
        return $platforms = Platform::all();
    }
}

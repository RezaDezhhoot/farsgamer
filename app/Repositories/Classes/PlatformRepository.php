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

    public function getAllAdminList($search, $pagination)
    {
        return Platform::latest('id')->search($search)->paginate($pagination);
    }

    public function find($id)
    {
        return Platform::findOrFail($id);
    }

    public function delete(Platform $platform)
    {
        return $platform->delete();
    }

    public function newPlatformObject()
    {
        return new Platform();
    }

    public function save(Platform $platform)
    {
        $platform->save();
        return $platform;
    }
}

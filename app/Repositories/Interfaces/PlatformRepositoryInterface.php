<?php
namespace App\Repositories\Interfaces;
use App\Models\Platform;
use Illuminate\Http\Request;

interface PlatformRepositoryInterface
{
    public function getAll();

    public function getAllAdminList($search , $pagination);

    public function find($id);

    public function delete(Platform $platform);

    public function newPlatformObject();

    public function save(Platform $platform);
}

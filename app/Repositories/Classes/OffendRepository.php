<?php


namespace App\Repositories\Classes;

use App\Models\Offend;
use App\Repositories\Interfaces\OffendRepositoryInterface;

class OffendRepository implements OffendRepositoryInterface
{

    public function create(array $data)
    {
        return Offend::create($data);
    }

    /**
     * @param $search
     * @param $pagination
     * @return mixed
     */
    public function getAllAdminList($search, $pagination)
    {
        return Offend::latest('id')->when($search,function ($query) use ($search){
            return $query->whereHas('user',function ($query) use ($search){
                return is_numeric($search) ? $query->where('phone',$search) : $query->where('user_name',$search);
            });
        })->paginate($pagination);
    }
}

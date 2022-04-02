<?php


namespace App\Repositories\Classes;

use App\Models\Send;
use App\Repositories\Interfaces\SendRepositoryInterface;

class SendRepository implements SendRepositoryInterface
{

    public function getByCondition($col, $operator, $value, $active = true)
    {
        return Send::active($active)->where($col,$operator,$value)->get();
    }

    public function availableStatus()
    {
        return Send::AVAILABLE;
    }

    public function getAllAdminList($status, $search, $pagination)
    {
        return Send::latest('id')->when($status,function ($query) use ($status){
            return $query->where('status',$status);
        })->search($search)->paginate($pagination);
    }

    public function getStatus()
    {
        return Send::getStatus();
    }

    public function find($id)
    {
        return Send::findOrFail($id);
    }

    public function delete(Send $send)
    {
        return $send->delete();
    }

    public function newSendObject()
    {
        return new Send();
    }

    public function save(Send $send)
    {
        $send->save();
        return $send;
    }
}

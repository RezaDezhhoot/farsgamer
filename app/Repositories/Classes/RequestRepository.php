<?php


namespace App\Repositories\Classes;

use App\Models\Request;
use App\Repositories\Interfaces\RequestRepositoryInterface;

class RequestRepository implements RequestRepositoryInterface
{

    /**
     * @param $status
     * @param $search
     * @param $phone
     * @param $user_name
     * @param $pagination
     * @return mixed
     */
    public function getAllAdminList($status , $search, $phone, $user_name, $pagination)
    {
        return Request::latest('id')->with(['user'])->when($status, function ($query) use ($status){
            return $query->where('status',$status);
        })->when($phone,function ($query) use ($phone){
            return $query->whereHas('user',function ($query) use ($phone){
                return $query->where('phone',$phone);
            });
        })->when($user_name,function ($query) use ($user_name){
            return $query->whereHas('user',function ($query) use ($user_name){
                return $query->where('user_name',$user_name);
            });
        })->search($search)->paginate($pagination);
    }

    /**
     * @return mixed
     */
    public static function getStatus()
    {
        return Request::getStatus();
    }

    /**
     * @param $col
     * @param $operator
     * @param $value
     * @return mixed
     */
    public function getByConditionCount($col, $operator, $value)
    {
        return Request::where("$col","$operator","$value")->count();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Request::findOrFail($id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request)
    {
        $request->save();
        return $request;
    }

    /**
     * @return mixed
     */
    public static function newStatus()
    {
        return Request::NEW;
    }

    /**
     * @return mixed
     */
    public static function rejectedStatus()
    {
        return Request::REJECTED;
    }

    /**
     * @return mixed
     */
    public static function settlementStatus()
    {
        return Request::SETTLEMENT;
    }
}

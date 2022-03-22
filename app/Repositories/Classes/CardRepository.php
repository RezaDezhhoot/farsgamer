<?php


namespace App\Repositories\Classes;

use App\Models\Card;
use App\Repositories\Interfaces\CardRepositoryInterface;


class CardRepository implements CardRepositoryInterface
{

    const s = 1;
    public function getAllAdminList($search, $status, $pagination  , $active = true)
    {
        return Card::active($active)->latest('id')->with(['user'])->when($search,function ($query) use ($search){
            return $query->wherehas('user',function ($query) use ($search){
                return $query->where('phone',$search);
            });
        })->when($status,function ($query) use ($status){
            return $query->where('status',$status);
        })->paginate($pagination);
    }

    public function getStatus()
    {
        return Card::getStatus();
    }

    public function getByCondition($col, $operator, $value, $active = true)
    {
        // TODO: Implement getByCondition() method.
    }

    public function getByConditionCount($col, $operator, $value, $active = true)
    {
        return Card::active($active)->where("$col","$operator","$value")->count();
    }

    public function delete(Card $card)
    {
        return $card->delete();
    }

    public function find($id, $active = true)
    {
        return Card::active($active)->findOrFail($id);
    }

    public function getBank()
    {
        return Card::bank();
    }

    public function save(Card $card)
    {
        $card->save();
        return $card;
    }
}
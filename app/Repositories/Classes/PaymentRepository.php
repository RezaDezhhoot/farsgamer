<?php


namespace App\Repositories\Classes;

use App\Models\Payment;
use App\Models\User;
use App\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public $payment;
    /**
     * @param $ip
     * @param $user
     * @param $status
     * @param $search
     * @param $pagination
     * @return mixed
     */
    public function getAllAdminList($ip, $user, $status, $search , $pagination)
    {
        return Payment::latest('id')->with(['user'])->when($ip,function ($query) use ($ip){
            return $query->wherehas('user',function ($query) use ($ip){
                return $query->where('ip',$ip);
            });
        })->when($user,function ($query) use ($user){
            return $query->wherehas('user',function ($query) use ($user){
                return
                    is_numeric($user) ?
                        $query->where('phone',$user) : $query->where('user_name',$user);
            });
        })->when($status,function ($query) use ($status){
            return $query->where('status_code',$status);
        })->search($search)->paginate($pagination);
    }

    /**
     * @return mixed
     */
    public static function getStatus()
    {
        return Payment::getStatus();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Payment::findOrFail($id);
    }

    /**
     * @param Payment $payment
     * @return mixed
     */
    public function delete(Payment $payment)
    {
        return $payment->delete();
    }

    public function create(User $user, array $data)
    {
        return $user->payments()->create($data);
        // TODO: Implement create() method.
    }

    public function whereCause(array $where)
    {
        return $this->payment->where([$where]);
        // TODO: Implement where() method.
    }

    public function newObject()
    {
        return $this->payment =  new Payment();
    }
}

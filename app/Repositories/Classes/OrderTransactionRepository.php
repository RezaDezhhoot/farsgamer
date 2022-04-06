<?php


namespace App\Repositories\Classes;

use App\Models\Category;
use App\Models\OrderTransaction;
use App\Models\OrderTransactionData;
use App\Models\OrderTransactionPayment;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderTransactionRepository implements OrderTransactionRepositoryInterface
{
    public function start($order,$commission)
    {
        $transaction = '';
        try {
            DB::beginTransaction();
            $transaction = OrderTransaction::create([
                'customer_id' => auth()->id(),
                'seller_id' => $order->user_id,
                'order_id' => $order->id,
                'status' => OrderTransaction::WAIT_FOR_CONFIRM,
                'timer' => Carbon::make(now())->addHours(0),
                'commission' => $commission['commission'],
                'intermediary' => $commission['intermediary'],
            ]);
            OrderTransactionData::updateOrCreate(['order_transaction_id' => $transaction->id] , ['name'=>uniqid()]);
            DB::commit();
        } catch (\Exception $e){
            DB::rollBack();
            return 0;
        }

        return $transaction;
    }

    public function getAllAdminList($status, $way, $search, $category, $pagination)
    {
        // TODO: Implement getAllAdminList() method.
        return OrderTransaction::latest('id')->with(['order'])->when($status, function ($query) use ($status,$way) {
            return $query->where('status', $status)->where('is_returned',$way);
        })->when($search, function ($query) use ($search) {
            return $query->where('id', $search);
        })->when($category,function ($query) use ($category){
            return $query->whereHas('order',function ($query) use ($category){
                return $query->where('category_id',$category);
            });
        })->when($way,function ($query) use ($way) {
            return $query->where('is_returned',$way);
        })->paginate($pagination);
    }

    public function getCount($status, $way)
    {
        // TODO: Implement getCount() method.
        return OrderTransaction::where('status', $status)->where('is_returned',$way)->count();
    }

    public static function getStatus($way)
    {
        // TODO: Implement getStatus() method.
        return OrderTransaction::getStatus($way);
    }

    public static function count()
    {
        // TODO: Implement count() method.
        return OrderTransaction::count();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
        return OrderTransaction::findOrFail($id);
    }

    public static function receiveStatus()
    {
        // TODO: Implement receiveStatus() method.
        return OrderTransaction::receiveStatus();
    }

    public static function confirm()
    {
        // TODO: Implement confirm() method.
        return OrderTransaction::WAIT_FOR_CONFIRM;
    }

    public static function pay()
    {
        // TODO: Implement pay() method.
        return OrderTransaction::WAIT_FOR_PAY;
    }

    public static function send()
    {
        // TODO: Implement send() method.
        return OrderTransaction::WAIT_FOR_SEND;
    }

    public static function receive()
    {
        // TODO: Implement receive() method.
        return OrderTransaction::WAIT_FOR_RECEIVE;
    }

    public static function noReceive()
    {
        // TODO: Implement receive() method.
        return OrderTransaction::WAIT_FOR_NO_RECEIVE;
    }

    public static function test()
    {
        // TODO: Implement test() method.
        return OrderTransaction::WAIT_FOR_TESTING;
    }

    public static function complete()
    {
        // TODO: Implement complete() method.
        return OrderTransaction::WAIT_FOR_COMPLETE;
    }

    public static function isReturned()
    {
        // TODO: Implement isReturned() method.
        return OrderTransaction::IS_RETURNED;
    }

    public static function sendingData()
    {
        // TODO: Implement sendingData() method.
        return OrderTransaction::WAIT_FOR_SENDING_DATA;
    }

    public function save(OrderTransaction $orderTransaction)
    {
        // TODO: Implement save() method.
        $orderTransaction->save();
        return $orderTransaction;
    }

    public static function cancel()
    {
        // TODO: Implement cancel() method.
        return OrderTransaction::IS_CANCELED;
    }

    public function saveData(OrderTransactionData $orderTransactionData)
    {
        // TODO: Implement saveData() method.
        $orderTransactionData->save();
        return $orderTransactionData;
    }

    public static function control()
    {
        // TODO: Implement control() method.
        return OrderTransaction::WAIT_FOR_CONTROL;
    }

    public static function returnedStatus()
    {
        // TODO: Implement returnedStatus() method.
        return OrderTransaction::returnedStatus();
    }

    public static function getTimer($status)
    {
        // TODO: Implement getTimer() method.
        $timer =  Setting::getSingleRow($status.'Timer') ?? 0;
        return $timer ? $timer : 0;
    }

    public function updateData(OrderTransaction $orderTransaction, array $data)
    {
        // TODO: Implement updateData() method.
        $orderTransaction->data()->update($data);
        return $orderTransaction->data;
    }

    public static function hasPayment()
    {
        // TODO: Implement hasPayment() method.
        return OrderTransactionPayment::SUCCESS;
    }

    public static function standardStatus()
    {
        // TODO: Implement standardStatus() method.
        return OrderTransaction::standardStatus();
    }

    public static function getFor()
    {
        // TODO: Implement getFor() method.
        return OrderTransaction::getFor();
    }

    public function getUserTransactions( $user)
    {
        return OrderTransaction::with(['order'])->where('status',OrderTransaction::WAIT_FOR_SEND)->
        where(function ($query) use ($user){
            $query->where('seller_id',$user->id)->orWhere('customer_id',$user->id);
        })->whereHas('order',function ($query){
            return $query->whereHas('category',function ($query){
                return $query->where('type',Category::PHYSICAL);
            });
        })->count();
        // TODO: Implement getUserTransactions() method.
    }

    public function getMyTransactions($use_id, Request $request)
    {
        return OrderTransaction::latest('id')->where('seller_id',$use_id)->orWhere('customer_id',$use_id)
            ->when($request['tab'],function ($query) use ($request , $use_id){
                if ($request['tab'] == 'seller')
                    return $query->where('seller_id',$use_id);
                elseif ($request['tab'] == 'customer')
                    return $query->where('customer_id',$use_id);
                else return $query;
        })->paginate(10);
        // TODO: Implement getMyTransactions() method.
    }

    public function getMyTransaction($use_id, $id)
    {
        return OrderTransaction::latest('id')->where('seller_id',$use_id)->orWhere('customer_id',$use_id)
            ->findOrfail($id);
        // TODO: Implement getMyTransaction() method.
    }

    public function update(array $condition , array $data,$orderTransaction = null)
    {
        if (!is_null($orderTransaction)){
            $orderTransaction->where($condition)->update($data);
            return $orderTransaction;
        } else
            OrderTransaction::where($condition)->update($data);

        return true;
        // TODO: Implement update() method.
    }

    public function newPayment(array $data)
    {
        return OrderTransactionPayment::create($data);
        // TODO: Implement newPayment() method.
    }

    public static function successPayment()
    {
        return OrderTransactionPayment::SUCCESS;
        // TODO: Implement successPayment() method.
    }
}

<?php


namespace App\Repositories\Classes;

use App\Models\OrderTransaction;
use App\Models\OrderTransactionData;
use App\Models\OrderTransactionPayment;
use App\Models\Setting;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use Carbon\Carbon;

class OrderTransactionRepository implements OrderTransactionRepositoryInterface
{
    public function start($order,$commission)
    {
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
        return OrderTransaction::returnedStatus();
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
        return OrderTransaction::IS_REQUESTED;
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
        OrderTransaction::returnedStatus();
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
        return $orderTransaction->data()->update($data);
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
}

<?php


namespace App\Repositories\Classes;

use App\Models\Category;
use App\Models\OrderTransaction;
use App\Models\OrderTransactionData;
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
        OrderTransactionData::updateOrCreate(['transaction_id'=>$transaction->id],['name'=>uniqid()]);
        return $transaction;
    }

}

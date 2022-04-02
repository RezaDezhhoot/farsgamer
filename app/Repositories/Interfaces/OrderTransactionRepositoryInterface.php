<?php
namespace App\Repositories\Interfaces;

use App\Models\OrderTransaction;
use App\Models\OrderTransactionData;
use App\Models\User;
use Illuminate\Http\Request;

interface OrderTransactionRepositoryInterface
{
    public function start($order,$commission);

    public function getAllAdminList($status , $way , $search , $category , $pagination);

    public function getCount($status , $way);

    public static function getStatus($way);

    public static function count();

    public function find($id);

    public static function receiveStatus();

    public static function isReturned();

    public static function sendingData();

    public static function cancel();

    public static function control();

    public static function confirm();

    public static function pay();

    public static function send();

    public static function receive();

    public static function noReceive();

    public static function complete();

    public static function test();

    public function save(OrderTransaction $orderTransaction);

    public function saveData(OrderTransactionData $orderTransactionData);

    public static function returnedStatus();

    public static function getTimer($status);

    public function updateData(OrderTransaction $orderTransaction , array  $data);

    public static function hasPayment();

    public static function standardStatus();

    public static function getFor();

    public function getUserTransactions( $user);

    public function getMyTransactions($use_id , Request $request);

    public function getMyTransaction($use_id , $id);

    public function update(array $condition , array $data , $model = null);

    public function newPayment(array $data);

    public static function successPayment();
}

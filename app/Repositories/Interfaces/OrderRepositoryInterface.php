<?php
namespace App\Repositories\Interfaces;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

interface OrderRepositoryInterface
{
    public function getHomeOrders(Request $request);

    public function getUserOrders(User $user , Request $request);

    public function getUserOrder(User $user , $id);

    public function getOrder($id , $active = true);

    public function getAllAdminList($status , $search , $category , $pagination);

    public function count();

    public function delete(Order $order);

    public function getCountWhere($where);

    public static function getStatus();

    public static function isRequestedStatus();

    public static function isFinishedStatus();

    public static function isConfirmedStatus();

    public static function isNewStatus();

    public function save(Order $order);

    public function attachParameters(Order $order , $parameters);

    public function syncParameters(Order $order , $parameters);

    public function attachPlatforms(Order $order , $platforms);

    public function syncPlatforms(Order $order , $platforms);

    public function deleteParameters(Order $order);

    public static function getNew();

    public function create(User $user , array  $data);

    public function update(Order $order , array $data);
}

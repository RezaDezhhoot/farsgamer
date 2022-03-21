<?php
namespace App\Repositories\Interfaces;
use Illuminate\Http\Request;

interface OrderRepositoryInterface
{
    public function getHomeOrders(Request $request);

    public function getOrder($id);
}

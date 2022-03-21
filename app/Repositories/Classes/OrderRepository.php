<?php


namespace App\Repositories\Classes;


use App\Helper\Helper;
use App\Models\Category;
use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Http\Request;

class OrderRepository implements OrderRepositoryInterface
{
    public function getHomeOrders(Request $request)
    {
        $orders = Order::active(true)->with(['category','platforms','parameters']);
        $orders = $orders->when($request['q'],function ($query) use ($request){
            return $query->whereHas('category',function ($query) use ($request) {
                return $query->where('slug','LIKE','%'.$request['q'].'%')->orWhere('title','LIKE','%'.$request['q'].'%');
            })->orWhereHas('platforms',function ($query)use ($request){
                return $query->where('slug','LIKE','%'.$request['q'].'%');
            })->orWhere('slug','LIKE','%'.$request['q'].'%');
        })->when($request['category'],function ($query) use ($request) {
            $sub_categories_id = Helper::array_value_recursive('id',Category::find($request['category']));
            return $query->whereIn('category_id',$sub_categories_id);
        })->when($request['platform'],function ($query) use ($request){
            return $query->whereHas('platforms',function ($query) use ($request) {
                return $query->where('slug',$request['category']);
            });
        })->orderBy($request['view'] == 1 ? 'view_count' : 'id' , 'desc')->get();

        return $orders;
    }

    public function getOrder($id , $active = true)
    {
        return Order::active($active)->findOrFail($id);
    }
}

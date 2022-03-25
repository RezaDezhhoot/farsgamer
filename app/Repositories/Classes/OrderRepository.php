<?php


namespace App\Repositories\Classes;


use App\Helper\Helper;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderParameter;
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

    /**
     * @param $status
     * @param $search
     * @param $category
     * @param $pagination
     * @return mixed
     */
    public function getAllAdminList($status, $search, $category , $pagination)
    {
        return Order::latest('id')->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })->when($search, function ($query) use ($search) {
            return is_numeric($search) ?
                $query->where('id', (int)$search) : $query->where('slug', $search);
        })->when($category,function ($query) use ($category) {
            return $query->where('category_id',$category);
        })->paginate($pagination);
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return Order::count();
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function delete(Order $order)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param $where
     * @return mixed
     */
    public function getCountWhere($where)
    {
        return Order::where('status', $where)->count();
    }

    /**
     * @return mixed
     */
    public static function getStatus()
    {
        return Order::getStatus();
    }

    /**
     * @return mixed
     */
    public static function isRequestedStatus()
    {
        return Order::IS_REQUESTED;
    }

    /**
     * @return mixed
     */
    public static function isFinishedStatus()
    {
        return Order::IS_FINISHED;
    }

    public static function isConfirmedStatus()
    {
        // TODO: Implement isConfirmedStatus() method.
        return Order::IS_CONFIRMED;
    }

    public function save(Order $order)
    {
        $order->save();
        return $order;
    }

    public function attachParameters(Order $order, $parameters)
    {
        $order->parameters()->attach($parameters);
    }

    public function syncParameters(Order $order, $parameters)
    {
        $order->parameters()->sync($parameters);
    }

    public function attachPlatforms(Order $order, $platforms)
    {
        $order->platforms()->attach($platforms);
    }

    public function syncPlatforms(Order $order, $platforms)
    {
        $order->platforms()->sync($platforms);
    }

    public function deleteParameters(Order $order)
    {
        return OrderParameter::where('order_id',$order->id)->delete();
    }

    public static function getNew()
    {
        // TODO: Implement getNew() method.
        return Order::getNew();
    }
}

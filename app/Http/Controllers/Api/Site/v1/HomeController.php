<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CategoryCollection;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\v1\OrderCollection;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    private $orderRepository , $categoryRepository , $settingRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository ,
        CategoryRepositoryInterface $categoryRepository ,
        SettingRepositoryInterface $settingRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->categoryRepository = $categoryRepository;
        $this->settingRepository = $settingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $most_categories = $this->categoryRepository->getMostUsedCategories();
        $orders = $this->orderRepository->getHomeOrders($request);

        return response([
            'data' => [
                'orders' => new OrderCollection($orders),
                'most_order_categories' => new CategoryCollection($most_categories),
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }
}
<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CategoryCollection;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
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
     * @param Request $request
     * @return Application|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $most_used_categories = $this->categoryRepository->getMostUsedCategories();
        $orders = $this->orderRepository->getHomeOrders($request);
        return response([
            'data' => [
                'orders' => [
                    'records' => new OrderCollection($orders),
                    'paginate' => [
                        'total' => $orders->total(),
                        'count' => $orders->count(),
                        'per_page' => $orders->perPage(),
                        'current_page' => $orders->currentPage(),
                        'total_pages' => $orders->lastPage()
                    ],
                ],
                'most_used_categories' => [
                    'record' => new CategoryCollection($most_used_categories)
                ],
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }
}

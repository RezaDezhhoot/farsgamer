<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CategoryCollection;
use App\Http\Resources\v1\NotificationCollection;
use App\Http\Resources\v1\User;
use App\Models\Setting;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\PlatformRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class BasicController extends Controller
{
    private $platformRepository , $categoryRepository , $settingRepository , $userRepository;

    public function __construct(
        PlatformRepositoryInterface $repository  ,
        CategoryRepositoryInterface $categoryRepository ,
        SettingRepositoryInterface $settingRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->platformRepository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->settingRepository = $settingRepository;
        $this->userRepository = $userRepository;
    }

    public function sidebar()
    {
        $categories = $this->categoryRepository->getBaseCategories();
        $platforms = $this->platformRepository->getAll();
        $i = 0;
        foreach ($categories as $category){
            if ($i >= $this->settingRepository->getSiteFaq('categoryHomeCount')) break;

            $sub_categories_id = $this->array_value_recursive('id',$category->toArray()['children_recursive']);
            $sub_categories =  $this->categoryRepository->findMany($sub_categories_id,true);
            $category->sub_categories = $sub_categories;
            $i++;
        }
        return response([
            'data' => [
                'sidebar' => [
                    'categories' => new CategoryCollection($categories),
                    'platforms' => $platforms,
                    'contact_links' => $this->settingRepository->getSiteFaq('contact',[]),
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function base()
    {
        return response([
            'data'=> [
                'base' => [
                    'site_name' => $this->settingRepository->getSiteFaq('name'),
                    'title' => $this->settingRepository->getSiteFaq('title'),
                    'seoDescription' => $this->settingRepository->getSiteFaq('seoDescription'),
                    'seoKeywords' =>  $this->settingRepository->getSiteFaq('seoKeyword'),
                    'logo' => asset($this->settingRepository->getSiteFaq('logo')),
                    'tel' => $this->settingRepository->getSiteFaq('tel'),
                    'notification' => $this->settingRepository->getSiteFaq('notification'),
                    'price_unit' => 'toman',
                ]
            ]
            ,'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function user()
    {
        return response([
            'data'=> [
                'user_data' => [
                    'user' => new User(auth()->user()) ,
                    'notifications' =>  new NotificationCollection($this->userRepository->getLastNotifications(auth()->user(),10)),
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function loginImage()
    {
        return \response([
            'data' => [
                'loginImage' => Setting::getSingleRow('logInImage')
            ]
        ]);
    }
}

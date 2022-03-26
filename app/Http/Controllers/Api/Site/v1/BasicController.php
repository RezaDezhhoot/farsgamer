<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CategoryCollection;
use App\Http\Resources\v1\NotificationCollection;
use App\Http\Resources\v1\User;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\PlatformRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class BasicController extends Controller
{
    private $platformRepository , $categoryRepository , $settingRepository;

    public function __construct(
        PlatformRepositoryInterface $repository  ,
        CategoryRepositoryInterface $categoryRepository ,
        SettingRepositoryInterface $settingRepository
    )
    {
        $this->platformRepository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->settingRepository = $settingRepository;
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
                'categories' => new CategoryCollection($categories),
                'platforms' => $platforms,
                'contact_links' => $this->settingRepository->getSiteFaq('contact',[]),
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function base()
    {
        return response([
            'data'=> [
                'site_name' => $this->settingRepository->getSiteFaq('name'),
                'title' => $this->settingRepository->getSiteFaq('title'),
                'seoDescription' => $this->settingRepository->getSiteFaq('seoDescription'),
                'seoKeywords' =>  $this->settingRepository->getSiteFaq('seoKeyword'),
                'logo' => asset($this->settingRepository->getSiteFaq('logo')),
                'tel' => $this->settingRepository->getSiteFaq('tel'),
                'notification' => $this->settingRepository->getSiteFaq('notification'),
                'user_data' => [
                    'user' => auth('api')->check() ? new User(auth('api')->user()) : [] ,
                    'notifications' =>auth('api')->check() ? new NotificationCollection(auth('api')->user()->alerts) : [],
                ],
            ]
            ,'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function userSidebar()
    {

    }
}

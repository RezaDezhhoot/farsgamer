<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class FagController extends Controller
{
    private $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function about()
    {
        $public = [
            'title' => 'درباره ما'
        ];
        return response([
            'data' => [
                'about_text' => $this->settingRepository->getSiteFaq('aboutUs'),
                'about_images' => $this->settingRepository->getSiteFaq('aboutUsImages'),
                'copyRight' => $this->settingRepository->getSiteFaq('copyRight'),
                'head' => $public,
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function contact()
    {
        $public = [
            'title' => 'ارتباط با ما'
        ];
        return response([
            'data' => [
                'tel' => $this->settingRepository->getSiteFaq('tel'),
                'email' => $this->settingRepository->getSiteFaq('email'),
                'address' => $this->settingRepository->getSiteFaq('address'),
                'contact_links' => $this->settingRepository->getSiteFaq('contact',[]),
                'googleMap' => $this->settingRepository->getSiteFaq('googleMap'),
                'head' => $public,
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function fag()
    {
        $public = [
            'title' => 'سوالات متداول'
        ];
        return response([
            'data' => [
                $this->settingRepository->getFagList(),
                'head' => $public,
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function law()
    {
        $public = [
            'title' => 'قوانین'
        ];
        return response([
            'data' => [
                'law' => $this->settingRepository->getSiteLaw('law'),
                'chatLaw' => $this->settingRepository->getSiteLaw('chatLaw'),
                'head' => $public,
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

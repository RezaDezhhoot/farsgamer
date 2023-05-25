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
        return response([
            'data' => [
                'about' => [
                    'about_text' => $this->settingRepository->getSiteFaq('aboutUs'),
                    'about_images' => collect(explode(',',$this->settingRepository->getSiteFaq('aboutUsImages')))->map(function ($item) {
                        return asset($item);
                    }),
                    'copyRight' => $this->settingRepository->getSiteFaq('copyRight'),
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function contact()
    {
        return response([
            'data' => [
                'contact' => [
                    'tel' => $this->settingRepository->getSiteFaq('tel'),
                    'email' => $this->settingRepository->getSiteFaq('email'),
                    'address' => $this->settingRepository->getSiteFaq('address'),
                    'contact_links' => $this->settingRepository->getSiteFaq('contact',[]),
                    'googleMap' => $this->settingRepository->getSiteFaq('googleMap'),
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function fag()
    {
        return response([
            'data' => [
               'fag' => [
                   'questions' => $this->settingRepository->getFagList(),
               ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function law()
    {
        return response([
            'data' => [
                'laws' => [
                    'laws' => $this->settingRepository->getSiteLaw('law'),
                    'description' =>  $this->settingRepository->getSiteLaw('description_law'),
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function chatLaw()
    {
        return response([
            'data' => [
                'laws' => [
                    'chat_laws' => $this->settingRepository->getSiteLaw('chatLaw'),
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function phoneLaw()
    {
        return response([
            'data' => [
                'laws' => [
                    'phone_laws' => $this->settingRepository->getSiteLaw('phoneLaw'),
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function transactionLaw()
    {
        return response([
            'data' => [
                'laws' => [
                    'transaction_laws' => $this->settingRepository->getSiteLaw('transactionLaw'),
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

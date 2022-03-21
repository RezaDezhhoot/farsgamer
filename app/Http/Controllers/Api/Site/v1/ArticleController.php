<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Article;
use App\Http\Resources\v1\ArticleCollection;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    private $articleRepository , $settingRepository;
    public function __construct
    (
        ArticleRepositoryInterface $articleRepository,
        SettingRepositoryInterface $settingRepository
    )
    {
        $this->articleRepository = $articleRepository;
        $this->settingRepository = $settingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $public = [
            'title' => 'مقالات',
        ];
        return response([
            'data' => [
                'articles' =>new ArticleCollection($this->articleRepository->getAll($request)),
                'head' => $public
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($slug)
    {
        $article = $this->articleRepository->getArticle('slug',$slug);
        $public = [
            'title' => $article->title,
            'seoDescription' => $article->seo_description,
            'seoKeywords' => $article->seo_keywords,
            'logo' => asset($this->settingRepository->getSiteFaq('logo')),
        ];
        return response([
            'data' => [
                'article' => new Article($article),
                'head' => $public
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }

    public function storeComment($slug,Request $request)
    {
        $article = $this->articleRepository->getArticle('slug',$slug);
        $rateKey = 'verify-attempt:' . auth()->id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 4)) {
            return
                response([
                    'data' => [
                        'message' => 'زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.'
                    ],
                    'status' => 'error'
                ],Response::HTTP_UNAUTHORIZED);
        }
        RateLimiter::hit($rateKey, 3 * 60 * 60);
        $request['user_id'] = auth()->id();
        $validator = Validator::make($request->all(),[
            'content' => 'required|max:450|string'
        ],[],[
            'content' => 'متن',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNAUTHORIZED);
        }
        $this->articleRepository->registerComment($article,$request->all());
        return response([
            'data' =>  [
                'message' => 'کامنت با موفقیت ثبت شد.'
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

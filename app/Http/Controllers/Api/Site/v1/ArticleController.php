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
        $articles = $this->articleRepository->getAll($request);
        return response([
            'data' => [
                'articles' => [
                    'records' => new ArticleCollection($articles),
                    'paginate' => [
                        'total' => $articles->total(),
                        'count' => $articles->count(),
                        'per_page' => $articles->perPage(),
                        'current_page' => $articles->currentPage(),
                        'total_pages' => $articles->lastPage()
                    ],
                ],
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
        return response([
            'data' => [
                'article' => [
                    'record' => new Article($article),
                ]
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }

    public function storeComment($slug,Request $request)
    {
        $article = $this->articleRepository->getArticle('slug',$slug);
        $rateKey = 'verify-attempt:' . auth('api')->id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 6)) {
            return
                response([
                    'data' => [
                        'message' => [
                            'user' => ['زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.']
                        ]
                    ],
                    'status' => 'error'
                ],Response::HTTP_TOO_MANY_REQUESTS);
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
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->articleRepository->registerComment($article,$request->all());
        return response([
            'data' =>  [
                'message' => [
                    'content' => ['کامنت با موفقیت ثبت شد.']
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}

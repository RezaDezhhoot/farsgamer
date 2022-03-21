<?php


namespace App\Repositories\Classes;

use App\Models\Article;
use App\Models\Comment;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Http\Request;


class ArticleRepository implements ArticleRepositoryInterface
{

    public function getAll(Request $request , $active = true)
    {
        return Article::active($active)->search($request['q'])->get();
    }

    public function findArticle($id , $active = true)
    {
        return Article::active($active)->findOrFail($id);
    }

    public function getArticle($col, $value, $active = true)
    {
        return Article::active($active)->where($col,$value)->first();
    }

    public function registerComment(Article $article, array $data )
    {
        $data['user_id'] = auth()->id();
        $article->comments()->create($data);
    }
}

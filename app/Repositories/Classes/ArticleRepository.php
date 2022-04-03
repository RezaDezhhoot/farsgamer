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
        return Article::latest('id')->active($active)->search($request['q'])->paginate(35);
    }

    public function findArticle($id , $active = true)
    {
        return Article::active($active)->findOrFail($id);
    }

    public function getArticle($col, $value, $active = true)
    {
        return Article::active($active)->where($col,$value)->firstOrFail();
    }

    public function registerComment(Article $article, array $data )
    {
        $data['user_id'] = auth('api')->id();
        return $article->comments()->create($data);
    }

    public function getAllAdmin($search, $status, $pagination)
    {
        return Article::latest('id')->when($status,function ($query) use ($status){
            return $query->where('status',$status);
        })->search($search)->paginate($pagination);
    }

    public function delete(Article $article)
    {
        return $article->delete();
    }

    public function deleteComments(Article $article)
    {
        return $article->comments()->delete();
    }

    public function getStatus()
    {
        return Article::getStatus();
    }

    public function getNewObject()
    {
        return new Article();
    }

    public function update(Article $article, array $data)
    {
        return $article->update($data);
    }

    public function save(Article $article)
    {
        $article->save();
        return $article;
    }

    public function syncCategories(Article $article , $categories)
    {
        $article->categories()->sync($categories);
    }

    public function attachCategories(Article $article, $categories)
    {
        $article->categories()->attach($categories);
    }
}

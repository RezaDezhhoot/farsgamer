<?php


namespace App\Repositories\Classes;

use App\Models\ArticleCategory;
use App\Repositories\Interfaces\ArticleCategoryRepositoryInterface;

class ArticleCategoryRepository implements ArticleCategoryRepositoryInterface
{
    public function getAll($active = true)
    {
        return ArticleCategory::active($active)->get();
    }

    public function getAllAdminList($search, $status, $pagination, $active = true)
    {
        return ArticleCategory::active($active)->latest('id')->when($status,function ($query) use ($status){
            return $query->where('status',$status);
        })->search($search)->paginate($pagination);
    }

    public function getStatus()
    {
        return ArticleCategory::getStatus();
    }

    public function delete(ArticleCategory $articleCategory)
    {
        return $articleCategory->delete();
    }

    public function find($id, $active = true)
    {
        return ArticleCategory::active($active)->findOrFail($id);
    }

    public function getByConditions($col, $orator, $value)
    {
        return ArticleCategory::where("$col",$orator,$value);
    }

    public function newCategoryObject()
    {
        return new ArticleCategory();
    }

    public function save(ArticleCategory $model)
    {
        $model->save();
        return $model;
    }
}

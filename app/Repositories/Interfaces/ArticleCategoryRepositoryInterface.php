<?php

namespace App\Repositories\Interfaces;


use App\Models\ArticleCategory;
use Illuminate\Http\Request;

interface ArticleCategoryRepositoryInterface
{
    public function getAll($active = true);

    public function getAllAdminList($search , $status , $pagination, $active = true);

    public function getStatus();

    public function delete(ArticleCategory $articleCategory);

    public function find($id , $active = true);

    public function getByConditions($col , $orator , $value);

    public function newCategoryObject();

    public function save(ArticleCategory $model);
}

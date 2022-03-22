<?php

namespace App\Repositories\Interfaces;

use App\Models\Article;
use Illuminate\Http\Request;

interface ArticleRepositoryInterface
{
    public function getAll(Request $request , $active = true);

    public function findArticle($id , $active = true);

    public function getArticle($col,$value , $active = true);

    public function registerComment(Article $article , array $data );

    public function getAllAdmin($search , $status, $pagination);

    public function delete(Article $article);

    public function deleteComments(Article $article);

    public function getStatus();

    public function getNewObject();

    public function update(Article $article , array $data);

    public function save(Article $article);

    public function syncCategories(Article $article , $categories);

    public function attachCategories(Article $article , $categories);
}

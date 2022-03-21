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
}

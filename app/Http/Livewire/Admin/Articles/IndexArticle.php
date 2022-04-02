<?php

namespace App\Http\Livewire\Admin\Articles;

use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Http\Livewire\BaseComponent;
use Livewire\WithPagination;

class IndexArticle extends BaseComponent
{
    use WithPagination ;
    protected $queryString = ['status'];
    public $status;
    public  $data = [] , $placeholder = 'عنوان یا نام مستعار';

    public function render(ArticleRepositoryInterface $articleRepository)
    {
        $this->authorizing('show_articles');
        $articles = $articleRepository->getAllAdmin($this->search,$this->status,$this->pagination);
        $this->data['status'] = $articleRepository->getStatus();
        return view('livewire.admin.articles.index-article',['articles'=>$articles])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , ArticleRepositoryInterface $articleRepository)
    {
        $this->authorizing('delete_articles');
        $article = $articleRepository->findArticle($id,false);
        $articleRepository->deleteComments($article);
        $articleRepository->delete($article);
    }
}

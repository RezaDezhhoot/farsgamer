<?php

namespace App\Http\Livewire\Admin\ArticlesCategories;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\ArticleCategoryRepositoryInterface;
use Livewire\WithPagination;

class IndexArticleCategory extends BaseComponent
{
    use WithPagination ;

    protected $queryString = ['status'];
    public $status;
    public $data = [] ,$placeholder = 'عنوان یا نام مستعار';

    public function render(ArticleCategoryRepositoryInterface $articleCategoryRepository)
    {
        $this->authorizing('show_article_categories');
        $categories = $articleCategoryRepository->getAllAdminList($this->search,$this->status,$this->pagination,false);
        $this->data['status'] = $articleCategoryRepository->getStatus();
        return view('livewire.admin.articles-categories.index-article-category',['categories'=>$categories])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , ArticleCategoryRepositoryInterface $articleCategoryRepository)
    {
        $this->authorizing('delete_article_categories');
        $article = $articleCategoryRepository->find($id,false);
        $articleCategoryRepository->delete($article);
    }
}

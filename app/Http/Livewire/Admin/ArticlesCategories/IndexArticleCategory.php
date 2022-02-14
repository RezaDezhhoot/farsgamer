<?php

namespace App\Http\Livewire\Admin\ArticlesCategories;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ArticleCategory;

class IndexArticleCategory extends Component
{
    use WithPagination , AuthorizesRequests;

    protected $queryString = ['status'];
    public $status;
    public $pagination = 10 , $search , $data = [] ,$placeholder = 'عنوان یا نام مستعار';

    public function render()
    {
        $this->authorize('show_article_categories');
        $categories = ArticleCategory::latest('id')->when($this->status,function ($query){
            return $query->where('status',$this->status);
        })->search($this->search)->paginate($this->pagination);
        $this->data['status'] = ArticleCategory::getStatus();
        return view('livewire.admin.articles-categories.index-article-category',['categories'=>$categories])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_article_categories');
        ArticleCategory::findOrFail($id)->delete();
    }
}

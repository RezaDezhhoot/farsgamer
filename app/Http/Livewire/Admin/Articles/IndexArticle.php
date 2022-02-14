<?php

namespace App\Http\Livewire\Admin\Articles;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Article;

class IndexArticle extends Component
{
    use WithPagination , AuthorizesRequests;
    protected $queryString = ['status'];
    public $status;
    public $pagination = 10 , $search , $data = [] , $placeholder = 'عنوان یا نام مستعار';

    public function render()
    {
        $this->authorize('show_articles');
        $articles = Article::latest('id')->when($this->status,function ($query){
            return $query->where('status',$this->status);
        })->search($this->search)->paginate($this->pagination);
        $this->data['status'] = Article::getStatus();
        return view('livewire.admin.articles.index-article',['articles'=>$articles])->extends('livewire.admin.layouts.admin');
    }
    public function delete($id)
    {
        $this->authorize('delete_articles');
        Article::findOrFail($id)->delete();
    }
}

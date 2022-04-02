<?php

namespace App\Http\Livewire\Admin\Articles;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\ArticleCategoryRepositoryInterface;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class StoreArticle extends BaseComponent
{
    public $article , $mode , $header , $data = [] , $categories = [];
    public $slug ,$title,$main_image,$content,$seo_keywords,$seo_description,$score,$status,$commentable,$google_indexing;

    public function mount(
        ArticleRepositoryInterface $articleRepository , ArticleCategoryRepositoryInterface $articleCategoryRepository , $action , $id = null
    )
    {
        $this->authorizing('show_articles');
        if ($action == 'edit')
        {
            $this->article = $articleRepository->findArticle($id,false);
            $this->header = $this->article->title;
            $this->slug = $this->article->slug;
            $this->title = $this->article->title;
            $this->main_image = $this->article->main_image;
            $this->content = $this->article->content;
            $this->seo_keywords = $this->article->seo_keywords;
            $this->seo_description = $this->article->seo_description;
            $this->score = $this->article->score;
            $this->status = $this->article->status;
            $this->commentable = $this->article->commentable;
            $this->google_indexing = $this->article->google_indexing;
            $this->categories = $this->article->categories->pluck('id')->toArray();
        } elseif($action == 'create') $this->header = 'مقاله جدید';
        else abort(404);

        $this->mode = $action;
        $this->data['category'] = $articleCategoryRepository->getAll(false);
        $this->data['status'] = $articleRepository->getStatus();
    }

    public function deleteItem(ArticleRepositoryInterface $articleRepository)
    {
        $this->authorizing('delete_articles');
        $articleRepository->deleteComments($this->article);
        $articleRepository->delete($this->article);
        return redirect()->route('admin.article');
    }

    public function store(ArticleRepositoryInterface $articleRepository)
    {
        $this->authorizing('edit_articles');
        if ($this->mode == 'edit')
            $this->saveInDateBase($articleRepository,$this->article);
        else{
            $this->saveInDateBase( $articleRepository, $articleRepository->getNewObject());
            $this->reset(['slug','title','categories','main_image','content','seo_keywords','seo_description','score','status','commentable','google_indexing']);
        }
    }

    public function saveInDateBase($articleRepository,$model)
    {
        $fields = [
            'slug' => ['required','string','unique:articles,slug,'.($this->article->id ?? 0)],
            'title' => ['required','string','max:100'],
            'main_image' => ['nullable','string','max:250'],
            'content' => ['required','string','max:1000000'],
            'seo_keywords' => ['required','string','max:250'],
            'seo_description' => ['required','string','max:250'],
            'score' => ['required','numeric','between:0,5'],
            'status' => ['required','in:'.implode(',',array_keys($articleRepository->getStatus()))],
            'commentable' => ['nullable','boolean'],
            'google_indexing' => ['nullable','boolean'],
        ];
        $messages = [
            'slug' => 'نام مستعار',
            'title' => 'عنوان',
            'main_image' => 'تصویر',
            'content' => 'محتوا',
            'seo_keywords' => 'کلمات کلیدی',
            'seo_description' => 'توضیحات سئو',
            'score' => 'امیتاز',
            'status' => 'وضعیت',
            'commentable' => 'قابل کامنت گذاری',
            'google_indexing' => 'شناسایی به موتور های جستوجو',
        ];
        $this->validate($fields,[],$messages);
        $model->slug = $this->slug;
        $model->title = $this->title;
        $model->main_image = $this->main_image ?? '';
        $model->content = $this->content;
        $model->seo_keywords = $this->seo_keywords;
        $model->seo_description = $this->seo_description;
        $model->score = $this->score ?? 0;
        $model->status = $this->status;
        $model->commentable = $this->commentable ?? 0;
        $model->google_indexing = $this->google_indexing ?? 1;
        $model->user_id = Auth::id();
        $articleRepository->save($model);

        $this->mode == 'edit' ?
            $articleRepository->syncCategories($model,array_filter($this->categories)) : $articleRepository->attachCategories($model,array_filter($this->categories));

        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function render()
    {
        return view('livewire.admin.articles.store-article')
            ->extends('livewire.admin.layouts.admin');
    }
}

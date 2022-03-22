<?php

namespace App\Http\Livewire\Admin\ArticlesCategories;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\ArticleCategoryRepositoryInterface;

class StoreArticleCategory extends BaseComponent
{
    public $category , $mode , $header , $data = [];
    public $slug , $title , $logo , $status , $parent_id , $type;

    public function mount(ArticleCategoryRepositoryInterface $articleCategoryRepository , $action , $id = null)
    {
        $this->authorizing('show_article_categories');
        if ($action == 'edit')
        {
            $this->category = $articleCategoryRepository->find($id,false);
            $this->header = $this->category->title;
            $this->slug = $this->category->slug;
            $this->title = $this->category->title;
            $this->logo = $this->category->logo;
            $this->parent_id = $this->category->parent_id;
            $this->status = $this->category->status;
            $this->data['category'] = $articleCategoryRepository->getByConditions('id','=!',"$id")->pluck('title','id');
        } elseif($action == 'create') {
            $this->header = 'دسته جدید';
            $this->data['category'] = $articleCategoryRepository->getAll(false)->pluck('title','id');
        }
        else abort(404);

        $this->data['status'] = $articleCategoryRepository->getStatus();

        $this->mode = $action;
    }

    public function store(ArticleCategoryRepositoryInterface $articleCategoryRepository)
    {
        $this->authorizing('edit_article_categories');
        if ($this->mode == 'edit')
            $this->saveInDataBase($articleCategoryRepository,$this->category);
        else{
            $this->saveInDataBase($articleCategoryRepository,$articleCategoryRepository->newCategoryObject());
            $this->reset(['slug','title','logo','parent_id','status']);
        }
    }

    public function saveInDataBase($articleCategoryRepository, $model)
    {
        $fields = [
            'slug' => ['required','max:100','string','unique:articles_categories,slug,'.($this->category->id ?? 0)],
            'title' => ['required','string','max:100'],
            'logo' => ['nullable','string','max:250'],
            'parent_id' => ['nullable','exists:articles_categories,id'],
            'status' => ['required','in:'.implode(',',array_keys($articleCategoryRepository->getStatus()))],
        ];
        $messages = [
            'slug' => 'نام مستعار',
            'title' => 'عنوان',
            'logo' => 'ایکون',
            'parent_id' => 'دسته مادر',
            'status' => 'وضعیت',
        ];
        $this->validate($fields,[],$messages);
        $model->slug = $this->slug;
        $model->title = $this->title;
        $model->logo = $this->logo;
        $model->parent_id = $this->parent_id ? $this->parent_id : null;
        $model->status = $this->status;
        $articleCategoryRepository->save($model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem()
    {
        $this->authorizing('delete_article_categories');
        $this->category->delete();
        return redirect()->route('admin.articleCategory');
    }

    public function render()
    {
        return view('livewire.admin.articles-categories.store-article-category')
            ->extends('livewire.admin.layouts.admin');
    }
}

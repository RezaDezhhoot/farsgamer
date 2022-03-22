<?php

namespace App\Http\Livewire\Admin\Categories;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Livewire\WithPagination;

class IndexCategory extends BaseComponent
{
    use WithPagination;
    protected $queryString = ['status' ,'type','is_available'];
    public $status , $is_available , $type ,$placeholder = 'عنوان یا نام مستعار' , $data = [] ;

    public function render(CategoryRepositoryInterface $categoryRepository)
    {
        $this->authorizing('show_categories');
        $categories = $categoryRepository->getAllAdminList($this->search,$this->status,$this->is_available,$this->type,$this->pagination);
        $this->data['status'] = $categoryRepository->getStatus();
        $this->data['is_available'] = $categoryRepository->available();
        $this->data['type'] = $categoryRepository->type();
        return view('livewire.admin.categories.index-category',['categories'=>$categories])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , CategoryRepositoryInterface $categoryRepository)
    {
        $this->authorizing('delete_categories');
        $category = $categoryRepository->find($id,false);
        $categoryRepository->delete($category);
    }
}

<?php


namespace App\Repositories\Classes;

use App\Helper\Helper;
use App\Models\Category;
use App\Models\Setting;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getBaseCategories()
    {
        return Category::active(true)->with(['childrenRecursive'])->whereNull('parent_id')->get();
    }

    public function getMostUsedCategories()
    {
        return Category::withCount('orders')->active(true)
            ->take(Setting::getSingleRow('categoryHomeCount') ?? 120)->get()->sortByDesc('orders_count');
    }

    public function findMany($ids,$active = false)
    {
        Category::active($active)->findMany($ids);
    }

    public function getAllAdminList($search, $status, $is_available, $type, $pagination)
    {
        return Category::latest('id')->when($status,function ($query) use ($status){
            return $query->where('status',$status);
        })->when($is_available,function ($query) use ($is_available){
            return $query->where('is_available',$is_available);
        })->when($type,function ($query) use ($type){
            return $query->where('type',$type);
        })->search($search)->paginate($pagination);
    }

    public function getStatus()
    {
        return Category::getStatus();
    }

    public function type()
    {
        return Category::type();
    }

    public function available()
    {
        return Category::available();
    }

    public function delete(Category $category)
    {
        return $category->delete();
    }

    public function find($id , $active = true)
    {
        return Category::active($active)->findOrFail($id);
    }

    public function getByCondition($col , $operator, $value, $active = true)
    {
        return Category::active($active)->where($col,$operator,$value)->get();
    }

    public function getAll($active = true)
    {
        return Category::active($active)->all();
    }

    public function newCategoryObject()
    {
        return new Category();
    }

    public function save(Category $category)
    {
        $category->save();
        return $category;
    }

    public function syncSends(Category $category, $transfer)
    {
        $category->sends()->sync($transfer);
    }

    public function attachSends(Category $category, $transfer)
    {
        $category->sends()->attach($transfer);
    }

    public function syncPlatforms(Category $category, $platforms)
    {
        $category->platforms()->sync($platforms);
    }

    public function attachPlatforms(Category $category, $platforms)
    {
        $category->platforms()->attach($platforms);
    }
}

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
}

<?php
namespace App\Repositories\Interfaces;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function getBaseCategories();

    public function getMostUsedCategories();

    public function findMany($ids , $active = false);

    public function getAllAdminList($search , $status , $is_available , $type , $pagination);

    public function getStatus();

    public function type();

    public function available();

    public function delete(Category $category);

    public function find($id  , $active = true);

    public function getByCondition($col,$operator,$value,$active = true);

    public function getAll($active = true);

    public function newCategoryObject();

    public function save(Category $category);

    public function syncSends(Category $category , $transfer);

    public function attachSends(Category $category , $transfer);

    public function syncPlatforms(Category $category , $platforms);

    public function attachPlatforms(Category $category , $platforms);
}

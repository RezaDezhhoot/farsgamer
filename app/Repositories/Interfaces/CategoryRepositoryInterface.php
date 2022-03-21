<?php
namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function getBaseCategories();

    public function getMostUsedCategories();

    public function findMany($ids , $active = false);
}

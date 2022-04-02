<?php
namespace App\Repositories\Interfaces;

interface OffendRepositoryInterface
{
    public function create(array $data);

    public function getAllAdminList($search , $pagination);
}

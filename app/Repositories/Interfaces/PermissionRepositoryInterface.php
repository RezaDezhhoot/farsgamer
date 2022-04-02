<?php
namespace App\Repositories\Interfaces;

interface PermissionRepositoryInterface
{
    public function getAll();

    public function insert(array $data);
}

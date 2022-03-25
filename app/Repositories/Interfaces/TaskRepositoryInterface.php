<?php

namespace App\Repositories\Interfaces;


use App\Models\Task;

interface TaskRepositoryInterface
{
    public function getAllAdminList($search , $pagination);

    public function find($id);

    public function delete(Task $task);

    public function tasks();

    public function event();

    public function newTaskObject();

    public function save(Task $task);
}

<?php


namespace App\Repositories\Classes;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;


class TaskRepository implements TaskRepositoryInterface
{

    public function getAllAdminList($search, $pagination)
    {
        return Task::latest('id')->search($search)->paginate($pagination);
    }

    public function find($id)
    {
        return Task::findOrFail($id);
    }

    public function delete(Task $task)
    {
        return $task->delete();
    }


    public function tasks()
    {
        return Task::tasks();
    }

    public function event()
    {
        return Task::event();
    }

    public function newTaskObject()
    {
        return new Task();
    }

    public function save(Task $task)
    {
        $task->save();
        return $task;
    }
}

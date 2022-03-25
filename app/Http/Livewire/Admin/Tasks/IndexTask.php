<?php

namespace App\Http\Livewire\Admin\Tasks;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Livewire\WithPagination;

class IndexTask extends BaseComponent
{
    use WithPagination ;
    public $placeholder = 'عنوان';
    public function render(TaskRepositoryInterface $taskRepository)
    {
        $this->authorizing('show_tasks');
        $tasks = $taskRepository->getAllAdminList($this->search , $this->pagination);
        return view('livewire.admin.tasks.index-task',['tasks'=>$tasks])->extends('livewire.admin.layouts.admin');
    }

    public function delete(TaskRepositoryInterface $taskRepository , $id)
    {
        $this->authorizing('edit_tasks');
        $task = $taskRepository->find($id);
        $taskRepository->delete($task);
    }
}

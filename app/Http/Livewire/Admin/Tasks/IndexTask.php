<?php

namespace App\Http\Livewire\Admin\Tasks;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use App\Models\Task;

class IndexTask extends BaseComponent
{
    use WithPagination , AuthorizesRequests;
    public $pagination = 10 , $search , $placeholder = 'عنوان';
    public function render()
    {
        $this->authorize('show_tasks');
        $tasks = Task::latest('id')->search($this->search)->paginate($this->pagination);
        return view('livewire.admin.tasks.index-task',['tasks'=>$tasks])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('edit_tasks');
        Task::findOrFail($id)->delete();
    }
}

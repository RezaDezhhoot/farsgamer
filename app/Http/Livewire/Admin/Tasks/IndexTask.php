<?php

namespace App\Http\Livewire\Admin\Tasks;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Task;

class IndexTask extends Component
{
    use WithPagination , AuthorizesRequests;
    public $pagination = 10 , $search , $placeholder = 'عنوان';
    public function render()
    {
//        $this->authorize('tasks');
        $tasks = Task::latest('id')->search($this->search)->paginate($this->pagination);
        return view('livewire.admin.tasks.index-task',['tasks'=>$tasks])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        Task::findOrFail($id)->delete();
    }
}

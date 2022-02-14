<?php

namespace App\Http\Livewire\Admin\Tasks;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreTask extends BaseComponent
{
    use AuthorizesRequests;
    public $task , $name , $event = [] , $where , $value ,$data = [] , $header , $mode;

    public function mount($action , $id =null)
    {
        $this->authorize('show_tasks');
        if ($action == 'edit')
        {
            $this->task = Task::findOrFail($id);
            $this->header = $this->task->name;
            $this->name = $this->task->name;
            $this->event = $this->task->task;
            $this->where = $this->task->where;
            $this->value = $this->task->value;
        } elseif($action == 'create') $this->header = 'وظیفه جدید';
        else abort(404);

        $this->mode = $action;
        $this->data['task'] = Task::tasks();
        $this->data['code'] = Setting::codes();
        $this->data['event'] = Task::event();
    }

    public function store()
    {
        $this->authorize('edit_tasks');
        if ($this->mode == 'edit')
            $this->saveInDateBase($this->task);
        else {
            $this->saveInDateBase(new Task());
            $this->reset(['name','event','where','value']);
        }
    }

    public function saveInDateBase(Task $model)
    {
        $this->validate([
            'name' => ['required','string','max:250'],
            'event' => ['required','in:'.implode(',',array_keys(Task::event()))],
            'where' => ['required','string','max:250'],
            'value' => ['required','string','max:3600']
        ],[],[
            'name' => 'عنوان',
            'event' => 'رویداد',
            'where' => 'شرط',
            'value' => 'محتوا'
        ]);
        $model->name = $this->name;
        $model->task = $this->event;
        $model->where = $this->where;
        $model->value = $this->value;
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem()
    {
        $this->authorize('delete_tasks');
        $this->task->delete();
        return redirect()->route('admin.task');
    }

    public function render()
    {
        return view('livewire.admin.tasks.store-task')->extends('livewire.admin.layouts.admin');
    }
}

<?php

namespace App\Http\Livewire\Admin\Tasks;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;

class StoreTask extends BaseComponent
{
    public $task , $name , $event = [] , $where , $value ,$data = [] , $header , $mode;

    public function mount(SettingRepositoryInterface $settingRepository , TaskRepositoryInterface $taskRepository ,$action , $id =null)
    {
        $this->authorizing('show_tasks');
        if ($action == 'edit')
        {
            $this->task = $taskRepository->find($id);
            $this->header = $this->task->name;
            $this->name = $this->task->name;
            $this->event = $this->task->task;
            $this->where = $this->task->where;
            $this->value = $this->task->value;
        } elseif($action == 'create') $this->header = 'وظیفه جدید';
        else abort(404);

        $this->mode = $action;
        $this->data['task'] = $taskRepository->tasks();
        $this->data['code'] = $settingRepository->codes();
        $this->data['event'] = $taskRepository->event();
    }

    public function store( TaskRepositoryInterface $taskRepository)
    {
        $this->authorizing('edit_tasks');
        if ($this->mode == 'edit')
            $this->saveInDateBase($taskRepository,$this->task);
        else {
            $this->saveInDateBase($taskRepository,$taskRepository->newTaskObject());
            $this->reset(['name','event','where','value']);
        }
    }

    public function saveInDateBase($taskRepository, $model)
    {
        $this->validate([
            'name' => ['required','string','max:250'],
            'event' => ['required','in:'.implode(',',array_keys($taskRepository->event()))],
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
        $taskRepository->save($model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem(TaskRepositoryInterface $taskRepository)
    {
        $this->authorizing('delete_tasks');
        $taskRepository->delete($this->task);
        return redirect()->route('admin.task');
    }

    public function render()
    {
        return view('livewire.admin.tasks.store-task')->extends('livewire.admin.layouts.admin');
    }
}

<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;

class CreateLaw extends BaseComponent
{
    public $header , $law , $title , $mode , $content , $order;
    public function mount($action , $id = null)
    {
        if ($action == 'edit') {
            $this->law = Setting::where('name','law')->findOrFail($id);
            $this->title = $this->law->value['title'];
            $this->content = $this->law->value['content'];
            $this->order = $this->law->value['order'];
            $this->header = $this->title;
        } else $this->header = 'قانون جدید';

        $this->mode = $action;
    }

    public function store()
    {
        if ($this->mode == 'edit')
            $this->saveInDataBase($this->law);
        elseif ($this->mode == 'create'){
            $this->saveInDataBase(new Setting());
            $this->reset(['content','title','order']);
        }
    }

    public function saveInDataBase(Setting $model)
    {
        $this->validate([
            'content' => ['required','string','max:25000'],
            'title' => ['required','string','max:250'],
            'order' => ['required','integer','between:0,100000000']
        ],[],[
            'content' => 'قانون',
            'title' => 'عنوان',
            'order'=> 'نمایش',
        ]);
        $model->name = 'law';
        $model->value = json_encode(['content' => $this->content,'title' => $this->title , 'order' => $this->order]);
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }


    public function deleteItem()
    {
        $this->law->delete();
        return redirect()->route('admin.setting.law');
    }

    public function render()
    {
        return view('livewire.admin.settings.create-law')
            ->extends('livewire.admin.layouts.admin');
    }
}

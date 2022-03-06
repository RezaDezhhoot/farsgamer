<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Setting;

class CreateFag extends BaseComponent
{
    use AuthorizesRequests;
    public $header , $row , $question , $mode , $answer , $order , $category;
    public function mount($action , $id = null)
    {
        $this->authorize('show_settings_fag');
        if ($action == 'edit') {
            $this->row = Setting::where('name','question')->findOrFail($id);
            $this->question = $this->row->value['question'];
            $this->answer = $this->row->value['answer'];
            $this->order = $this->row->value['order'];
            $this->category = $this->row->value['category'];
        }

        $this->header = 'سوال';
        $this->mode = $action;
    }

    public function store()
    {
        $this->authorize('edit_settings_fag');
        if ($this->mode == 'edit')
            $this->saveInDataBase($this->row);
        elseif ($this->mode == 'create'){
            $this->saveInDataBase(new Setting());
            $this->reset(['question','answer','category','order']);
        }
    }

    public function saveInDataBase(Setting $model)
    {
        $this->validate([
            'question' => ['required','string','max:1000'],
            'answer' => ['required','string','max:1000'],
            'category' => ['required','string','max:250'],
            'order' => ['required','integer','between:0,10000000000']
        ],[],[
            'question' => 'سوال',
            'answer' => 'جواب',
            'category' => 'دسته',
            'order'=> 'نمایش',
        ]);
        $model->name = 'question';
        $model->value = json_encode(['question' => $this->question,'answer' => $this->answer,'category' => $this->category , 'order' => $this->order]);
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem()
    {
        $this->authorize('edit_settings_fag');
        $this->question->delete();
        return redirect()->route('admin.setting.law');
    }

    public function render()
    {
        return view('livewire.admin.settings.create-fag')
            ->extends('livewire.admin.layouts.admin');
    }
}

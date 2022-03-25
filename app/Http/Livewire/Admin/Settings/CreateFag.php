<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;

class CreateFag extends BaseComponent
{
    public $header , $row , $question , $mode , $answer , $order , $category;
    public function mount(SettingRepositoryInterface $settingRepository,$action , $id = null)
    {
        $this->authorizing('show_settings_fag');
        if ($action == 'edit') {
            $this->row = $settingRepository->find($id);
            $this->question = $this->row->value['question'];
            $this->answer = $this->row->value['answer'];
            $this->order = $this->row->value['order'];
            $this->category = $this->row->value['category'];
        }

        $this->header = 'سوال';
        $this->mode = $action;
    }

    public function store(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('edit_settings_fag');
        if ($this->mode == 'edit')
            $this->saveInDataBase($settingRepository,  $this->row);
        elseif ($this->mode == 'create'){
            $this->saveInDataBase($settingRepository ,$settingRepository->newSettingObject());
            $this->reset(['question','answer','category','order']);
        }
    }

    public function saveInDataBase($settingRepository ,  $model)
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
        $settingRepository->save($model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('edit_settings_fag');
        $settingRepository->delete($this->question);
        return redirect()->route('admin.setting.law');
    }

    public function render()
    {
        return view('livewire.admin.settings.create-fag')
            ->extends('livewire.admin.layouts.admin');
    }
}

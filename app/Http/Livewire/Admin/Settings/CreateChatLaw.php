<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;


class CreateChatLaw extends BaseComponent
{
    public $header , $law , $title , $mode , $content , $order;
    public function mount(SettingRepositoryInterface $settingRepository , $action , $id = null)
    {
        $this->authorizing('show_settings_chatLaw');
        if ($action == 'edit') {
            $this->law = $settingRepository->find($id);
            $this->title = $this->law->value['title'];
            $this->content = $this->law->value['content'];
            $this->order = $this->law->value['order'];
            $this->header = $this->title;
        } else $this->header = 'قانون چت جدید';

        $this->mode = $action;
    }

    public function store(SettingRepositoryInterface $settingRepository)
    {
        $this->authorize('edit_settings_chatLaw');
        if ($this->mode == 'edit')
            $this->saveInDataBase($settingRepository,$this->law);
        elseif ($this->mode == 'create'){
            $this->saveInDataBase($settingRepository,$settingRepository->newSettingObject());
            $this->reset(['content','title','order']);
        }
    }

    public function saveInDataBase($settingRepository , $model)
    {
        $this->validate([
            'content' => ['required','string','max:35000'],
            'title' => ['required','string','max:250'],
            'order' => ['required','integer','between:0,1000000000']
        ],[],[
            'content' => 'قانون',
            'title' => 'عنوان',
            'order'=> 'نمایش',
        ]);
        $model->name = 'chatLaw';
        $model->value = json_encode(['content' => $this->content,'title' => $this->title , 'order' => $this->order]);
        $settingRepository->save($model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }


    public function deleteItem(SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('edit_settings_chatLaw');
        $settingRepository->delete($this->law);
        return redirect()->route('admin.setting.law');
    }

    public function render()
    {
        return view('livewire.admin.settings.create-chat-law')
            ->extends('livewire.admin.layouts.admin');
    }
}

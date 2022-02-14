<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CreateChatLaw extends Component
{
    use AuthorizesRequests;
    public $header , $law , $title , $mode , $content , $order;
    public function mount($action , $id = null)
    {
        $this->authorize('show_settings_chatLaw');
        if ($action == 'edit') {
            $this->law = Setting::where('name','chatLaw')->findOrFail($id);
            $this->title = $this->law->value['title'];
            $this->content = $this->law->value['content'];
            $this->order = $this->law->value['order'];
            $this->header = $this->title;
        } else $this->header = 'قانون چت جدید';

        $this->mode = $action;
    }

    public function store()
    {
        $this->authorize('edit_settings_chatLaw');
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
            'content' => ['required','string','max:35000'],
            'title' => ['required','string','max:250'],
            'order' => ['required','numeric']
        ],[],[
            'content' => 'قانون',
            'title' => 'عنوان',
            'order'=> 'نمایش',
        ]);
        $model->name = 'chatLaw';
        $model->value = json_encode(['content' => $this->content,'title' => $this->title , 'order' => $this->order]);
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }


    public function deleteItem()
    {
        $this->authorize('edit_settings_chatLaw');
        $this->law->delete();
        return redirect()->route('admin.setting.law');
    }

    public function render()
    {
        return view('livewire.admin.settings.create-chat-law')
            ->extends('livewire.admin.layouts.admin');
    }
}

<?php

namespace App\Http\Livewire\Admin\Platforms;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use App\Models\Platform;

class StorePlatform extends Component
{
    use AuthorizesRequests;
    public $platform , $mode , $header;
    public $slug , $logo;

    public function mount($action , $id = null)
    {
        $this->authorize('show_platforms');
        if ($action == 'edit')
        {
            $this->platform = Platform::findOrFail($id);
            $this->header = $this->platform->slug;
            $this->slug = $this->platform->slug;
            $this->logo = $this->platform->logo;
        } elseif($action == 'create') $this->header = 'پلتفرم جدید';
        else abort(404);

        $this->mode = $action;
    }

    public function store()
    {
        $this->authorize('edit_platforms');
        if ($this->mode == 'edit')
            $this->saveInDataBase($this->platform);
        else {
            $this->saveInDataBase(new Platform());
            $this->reset(['slug','logo']);
        }
    }

    public function saveInDataBase(Platform $model)
    {
        $fields = [
            'slug' => ['required','max:100','string','unique:platforms,slug,'.($this->platform->id ?? 0)],
            'logo' => ['required','string','max:250'],
        ];
        $messages = [
            'slug' => 'نام مستعار',
            'logo' => 'ایکون',
        ];
        $this->validate($fields,[],$messages);
        $model->slug = $this->slug;
        $model->logo = $this->logo ?? '';
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
    public function deleteItem()
    {
        $this->authorize('delete_platforms');
        $this->platform->delete();
        return redirect()->route('admin.platform');
    }

    public function render()
    {
        return view('livewire.admin.platforms.store-platform')->extends('livewire.admin.layouts.admin');
    }
}

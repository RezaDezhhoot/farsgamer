<?php

namespace App\Http\Livewire\Admin\Platforms;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\PlatformRepositoryInterface;

class StorePlatform extends BaseComponent
{
    public $platform , $mode , $header;
    public $slug , $logo;

    public function mount(PlatformRepositoryInterface $platformRepository,$action , $id = null)
    {
        $this->authorizing('show_platforms');
        if ($action == 'edit')
        {
            $this->platform = $platformRepository->find($id);
            $this->header = $this->platform->slug;
            $this->slug = $this->platform->slug;
            $this->logo = $this->platform->logo;
        } elseif($action == 'create') $this->header = 'پلتفرم جدید';
        else abort(404);

        $this->mode = $action;
    }

    public function store(PlatformRepositoryInterface $platformRepository)
    {
        $this->authorizing('edit_platforms');
        if ($this->mode == 'edit')
            $this->saveInDataBase($platformRepository , $this->platform);
        else {
            $this->saveInDataBase($platformRepository,$platformRepository->newPlatformObject());
            $this->reset(['slug','logo']);
        }
    }

    public function saveInDataBase($platformRepository , $model)
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
        $platformRepository->save($model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
    public function deleteItem(PlatformRepositoryInterface $platformRepository)
    {
        $this->authorizing('delete_platforms');
        $platformRepository->delete($this->platform);
        return redirect()->route('admin.platform');
    }

    public function render()
    {
        return view('livewire.admin.platforms.store-platform')->extends('livewire.admin.layouts.admin');
    }
}

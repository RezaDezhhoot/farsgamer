<?php

namespace App\Http\Livewire\Admin\Sends;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use App\Models\Send;

class StoreSend extends Component
{
    use AuthorizesRequests;
    public $transfer , $mode , $header , $data = [];
    public $slug , $logo , $send_time_inner_city , $send_time_outer_city , $note , $pursuit , $status , $pursuit_web_site;

    public function mount($action  ,$id = null)
    {
        $this->authorize('show_sends');
        if ($action == 'edit')
        {
            $this->transfer = Send::findOrFail($id);
            $this->header = $this->transfer->slug;
            $this->slug = $this->transfer->slug;
            $this->logo = $this->transfer->logo;
            $this->send_time_inner_city = $this->transfer->send_time_inner_city;
            $this->send_time_outer_city = $this->transfer->send_time_outer_city;
            $this->note = $this->transfer->note;
            $this->pursuit = $this->transfer->pursuit;
            $this->status = $this->transfer->status;
            $this->pursuit_web_site = $this->transfer->pursuit_web_site;
        } elseif($action == 'create') $this->header = 'روش ارسال جدید';
        else abort(404);

        $this->mode = $action;
        $this->data['status'] = Send::getStatus();
    }

    public function store()
    {
        $this->authorize('edit_sends');
        if ($this->mode == 'edit')
            $this->saveInDataBase($this->transfer);
        else{
            $this->saveInDataBase(new Send());
            $this->reset(['slug','logo','send_time_inner_city','send_time_outer_city','note','pursuit','status','pursuit_web_site']);
        }
    }

    public function saveInDataBase(Send $model)
    {
        $fields = [
            'slug' => ['required','max:150','string','unique:sends,slug,'.($this->transfer->id ?? 0)],
            'logo' => ['required','string','max:150'],
            'send_time_inner_city' => ['required','numeric','between:0,99999.99999'],
            'send_time_outer_city' => ['required','numeric','between:0,99999.99999'],
            'note' => ['nullable','string','max:255'],
            'pursuit' => ['nullable'],
            'status' => ['required','in:'.Send::UNAVAILABLE.','.Send::AVAILABLE],
            'pursuit_web_site' => ['nullable','url','max:250']
        ];
        $messages = [
            'slug' => 'نام مستعار',
            'logo' => 'ایکون',
            'send_time_inner_city' => 'حداکثر زمان برای ارسال درون شهری',
            'send_time_outer_city' => 'حداکثر زمان برای ارسال برون شهری',
            'note' => 'توضیحات',
            'pursuit' => 'قابل پیگیری',
            'status' => 'وضعیت',
            'pursuit_web_site' => 'سایت مورد نظر برای پیگیری',
        ];
        $this->validate($fields,[],$messages);

        $model->slug = $this->slug;
        $model->logo = $this->logo;
        $model->send_time_inner_city = $this->send_time_inner_city;
        $model->send_time_outer_city = $this->send_time_outer_city;
        $model->note = $this->note;
        $model->pursuit = $this->pursuit ?? 0;
        $model->status = $this->status;
        $model->pursuit_web_site = $this->pursuit_web_site;
        $model->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');

    }

    public function deleteItem()
    {
        $this->authorize('delete_sends');
        $this->transfer->delete();
        return redirect()->route('admin.transfer');
    }

    public function render()
    {
        return view('livewire.admin.sends.store-send')->extends('livewire.admin.layouts.admin');
    }
}

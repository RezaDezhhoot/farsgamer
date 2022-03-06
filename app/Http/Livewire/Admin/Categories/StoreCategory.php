<?php

namespace App\Http\Livewire\Admin\Categories;

use App\Http\Livewire\BaseComponent;
use App\Models\Parameter;
use App\Models\Platform;
use App\Models\Send;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Category;
use App\Traits\Admin\FormBuilder;

class StoreCategory extends BaseComponent
{
    use AuthorizesRequests , FormBuilder;
    public $category , $mode , $header , $data = [];
    public $slug , $title , $logo , $default_image , $slider , $description , $seo_keywords , $seo_description , $guarantee_time,
    $send_time , $pay_time ,$receive_time,$sending_data_time , $parent_id , $status , $is_available , $type , $transfer  , $parameters = [] , $para , $paraID , $platforms = [],
    $commission , $intermediary , $control , $no_receive_time;

    public $paraLogo , $paraName , $paraType , $paraField , $paraStatus , $paraMax , $paraMin ;
    public function mount($action , $id = null)
    {
        $this->authorize('show_categories');
        if ($action == 'edit')
        {
            $this->category = Category::findOrFail($id);
            $this->header = $this->category->title;
            $this->slug = $this->category->slug;
            $this->title = $this->category->title;
            $this->logo = $this->category->logo;
            $this->default_image = $this->category->default_image;
            $this->slider = $this->category->slider;
            $this->description = $this->category->description;
            $this->seo_keywords = $this->category->seo_keywords;
            $this->seo_description = $this->category->seo_description;
            $this->guarantee_time = $this->category->guarantee_time;
            $this->send_time = $this->category->send_time;
            $this->pay_time = $this->category->pay_time;
            $this->receive_time = $this->category->receive_time;
            $this->no_receive_time = $this->category->no_receive_time;
            $this->sending_data_time = $this->category->sending_data_time;
            $this->parent_id = $this->category->parent_id;
            $this->form = json_decode($this->category->forms,true) ?? [];
            $this->status = $this->category->status;
            $this->is_available = $this->category->is_available;
            $this->type = $this->category->type;
            $this->transfer =  $this->category->sends->pluck('id','id')->toArray();
            $this->platforms =  $this->category->platforms->pluck('id','id')->toArray();
            $this->parameters = $this->category->parameters->toArray();
            $this->commission = $this->category->commission;
            $this->intermediary = $this->category->intermediary;
            $this->control = $this->category->control;
        } elseif($action == 'create') $this->header = 'دسته جدید';
        else abort(404);

        $this->mode = $action;
        $this->data['is_available'] = Category::available();
        $this->data['type'] = Category::type();
        $this->data['status'] = Category::getStatus();
        $this->data['category'] = Category::all()->pluck('title','id');
        $this->data['transfer'] = Send::where('status',Send::AVAILABLE)->get();
        $this->data['platform'] = Platform::all();
    }

    public function store()
    {
        $this->authorize('edit_categories');
        if ($this->mode == 'edit')
            $this->saveInDataBase($this->category);
        else{
            $this->saveInDataBase(new Category());
            $this->reset(['slug','title','logo','default_image','slider','description','seo_keywords','seo_description','guarantee_time',
                'send_time','parent_id','status','is_available','is_physical','parameters','transfer','platforms','form','control']);
        }
    }

    public function saveInDataBase(Category $model)
    {
        $fields = [
            'slug' => ['required','string','max:80','unique:categories,slug,'.($this->category->id ?? 0)],
            'title' => ['required','string','max:70'],
            'logo' => ['required','string','max:250'],
            'default_image' => ['required','string','max:250'],
            'slider' => ['required','string','max:250'],
            'description' => ['nullable','string','max:2500'],
            'seo_keywords' => ['required','string','max:250'],
            'seo_description' => ['required','string','max:250'],
            'send_time' => ['required','numeric','between:0,999999999.9999'],
            'guarantee_time' => ['required','numeric','between:0,999999999.9999'],
            'pay_time' => ['required','numeric','between:0,999999999.9999'],
            'receive_time' => ['nullable','numeric','between:0,999999999.9999'],
            'sending_data_time' => ['nullable','numeric','between:0,999999999.9999'],
            'no_receive_time' => ['nullable','numeric','between:0,999999999.9999'],
            'parent_id' => ['nullable','exists:categories,id'],
            'status' => ['required','in:'.Category::UNAVAILABLE.','.Category::AVAILABLE],
            'is_available' => ['required','in:'.Category::YES.','.Category::NO],
            'type' => ['required','in:'.Category::DIGITAL.','.Category::PHYSICAL],
            'form' => ['nullable','array'],
            'commission' => ['required','numeric','between:0,20'],
            'intermediary' => ['required','numeric','between:0,20'],
            'control' => ['required','boolean'],
        ];
        $messages = [
            'slug' => 'نام مستعار',
            'title' => 'عنوان',
            'logo' => 'ایکون',
            'default_image' => 'تصویر پیشفرض برای اگهی های بدون تصویر',
            'slider' => 'تصویر پسزمینه',
            'description' => 'توضیحات',
            'seo_keywords' => 'کلمات کلیدی',
            'seo_description' => 'توضیحات سئو',
            'send_time' => 'زمان لازم برای ارسال توسط فروشنده یا خریردار',
            'pay_time' => 'زمان لازم برای پرداخت',
            'receive_time' => 'زمان ارسال فروشنده یا خریدار',
            'guarantee_time' => 'زمان تست محصول',
            'no_receive_time' => 'زمان پیگیری در صورت عدم دریافت فروشنده یا خریردار',
            'sending_data_time' => 'زمان لازم برای ارسال اطلاعات توسط خریدار در مرحله مرجوعیت',
            'parent_id' => 'دسته مادر',
            'status' => 'وضعیت',
            'is_available' => 'نوع دسته',
            'type' => 'نوع محضولات',
            'form' => 'فرم ها',
            'commission' => 'کارمزد شبکه',
            'intermediary' => 'حق واسطه گری',
            'control' => 'نیاز به واسط',
        ];
        if ($this->commission + $this->intermediary > 20)
            return $this->addError('error','مجموع کارمزد و حق واسه گری نباید از 20٪ بیشتر باشد.');
        $this->validate($fields,[],$messages);
        $model->slug = $this->slug;
        $model->title = $this->title;
        $model->logo = $this->logo;
        $model->default_image = $this->default_image;
        $model->slider = $this->slider;
        $model->description = $this->description;
        $model->seo_keywords = $this->seo_keywords;
        $model->seo_description = $this->seo_description;
        $model->guarantee_time = $this->guarantee_time;
        $model->send_time = $this->send_time ?? 0;
        $model->pay_time = $this->pay_time ?? 0;
        $model->receive_time = $this->receive_time ?? 0;
        $model->sending_data_time = $this->sending_data_time ?? 0;
        $model->no_receive_time = $this->no_receive_time ?? 0;
        $model->parent_id = $this->parent_id;
        $model->status = $this->status;
        $model->commission = $this->commission;
        $model->intermediary = $this->intermediary;
        $model->is_available = $this->is_available;
        $model->type = $this->type;
        $model->control = $this->control ?? 0;
        $model->forms = json_encode($this->form);
        $model->save();

        if (!is_null($this->transfer))
            $this->mode == 'edit' ?
                $model->sends()->sync(array_filter($this->transfer)) : $model->sends()->attach(array_filter($this->transfer));

        if (!is_null($this->platforms))
            $this->mode == 'edit' ?
                $model->platforms()->sync(array_filter($this->platforms)) : $model->platforms()->attach(array_filter($this->platforms));

        if (!is_null($this->parameters)) {
            foreach ($this->parameters as $key => $value)
            {
                $parameter = $value['id'] == 0 ? new Parameter() : Parameter::find($value['id']);
                $parameter->category_id  = $model->id;
                $parameter->logo  = $value['logo'];
                $parameter->name  = $value['name'];
                $parameter->type  = $value['type'];
                $parameter->field  = $value['field'];
                $parameter->status  = $value['status'];
                $parameter->max  = $value['max'];
                $parameter->min  = $value['min'];
                $parameter->save();
                $this->parameters[$key]['id'] = $parameter->id;
            }
        }
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function render()
    {
        return view('livewire.admin.categories.store-category')->extends('livewire.admin.layouts.admin');
    }

    public function deleteItem()
    {
        $this->authorize('delete_categories');
        $this->category->delete();
        return redirect()->route('admin.category');
    }


    public function addParameter($title)
    {
        if ($title == 'new') {
            $this->para = 'پارامتر جدید';
            $this->paraID = 'new';
        }
        else{
            $this->para = $this->parameters[$title]['name'];
            $this->paraID = $title;
            $this->paraLogo = $this->parameters[$title]['logo'];
            $this->paraName = $this->parameters[$title]['name'];
            $this->paraType = $this->parameters[$title]['type'];
            $this->paraField = $this->parameters[$title]['field'];
            $this->paraStatus = $this->parameters[$title]['status'];
            $this->paraMax = $this->parameters[$title]['max'];
            $this->paraMin = $this->parameters[$title]['min'];
        }

        $this->emitShowModal('parameter');
    }

    public function storeParameter()
    {
        $fields = [
            'paraLogo' => ['required','string','max:250'],
            'paraName' => ['required','string','max:40'],
            'paraType' => ['required','in:number,text'],
            'paraField' => ['required','string','max:250'],
            'paraStatus' => ['required','in:available,unavailable'],
            'paraMax' => ['nullable','numeric','max:250'],
            'paraMin' => ['nullable','numeric','max:100'],
        ];
        $messages = [
            'paraLogo' => 'ایکون',
            'paraName' => 'عنوان',
            'paraType' => 'نوع ورودی',
            'paraField' => 'مقدار پیشفرض',
            'paraStatus' => 'وضعیت',
            'paraMax' => 'حداکثر مقدار',
            'paraMin' => 'حداقل مقدار',
        ];
        $this->validate($fields,[],$messages);
        $parameter = [
            'id' => $this->parameters[$this->paraID]['id'] ?? 0,
            'logo' => $this->paraLogo,
            'name' => $this->paraName,
            'type' => $this->paraType,
            'field' => $this->paraField,
            'status' => $this->paraStatus,
            'max' => $this->paraMax,
            'min' => $this->paraMin
        ];
        if ($this->paraID == 'new')
            array_push($this->parameters,$parameter);
        else
            $this->parameters[$this->paraID] = $parameter;
        $this->reset(['paraLogo','paraName','paraType','paraField','paraMax','paraMin','paraStatus']);
        $this->emitHideModal('parameter');
    }

    public function deleteParameter($key)
    {
        $this->authorize('edit_categories');
        $para = Parameter::find($this->parameters[$key]['id']);
        if ($para)
            $para->delete();
        unset($this->parameters[$key]);
    }
}

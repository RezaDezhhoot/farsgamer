<?php

namespace App\Http\Livewire\Site\Dashboard\Orders;

use App\Http\Livewire\BaseComponent;
use App\Models\Category;
use App\Models\Order;
use App\Models\Parameter;
use App\Models\Setting;
use App\Models\User;
use App\Traits\Admin\Sends;
use App\Traits\Admin\TextBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class StoreOrder extends BaseComponent
{
    public $order , $header , $user , $mode;
    public $slug , $category_id , $content , $price , $image , $gallery , $province , $city ,$result;
    public $parameters , $parameter = [] , $platforms , $platform = [];
    public $data = [];
    use WithFileUploads , Sends , TextBuilder;
    public function mount($action , $id = null)
    {
        $ban = Carbon::make(now())->diff(\auth()->user()->ban)->format('%r%i');
        if ($ban > 0) {
            $this->addError('error','متاسفانه حساب کابری شما به دلیل نقض قوانین برای مدتی محدود شده است.مدتی بعد دوباره تلاش کنید');
            return;
        }
        if ($action == 'edit') {
            $this->user = User::findOrFail(Auth::id());
            $this->order = $this->user->orders()->findOrFail($id);
            $this->slug = $this->order->slug;
            $this->category_id = $this->order->category_id;
            $this->content = $this->order->content;
            $this->price = $this->order->price;
            $this->image = $this->order->image;
            $this->gallery = $this->order->gallery;
            $this->province = $this->order->province;
            $this->city = $this->order->city;
            $this->result = $this->order->reslut;
            if ($this->order->category->status == Category::AVAILABLE && $this->order->category->is_available == 1)
            {
            $this->category_id = $this->order->category_id;
            $this->parameter = $this->order->parameter->pluck('value','parameter_id');
            $this->platform = $this->order->platform->pluck('slug','id');
        }
        else
            $this->category_id = '';

            $this->data['status'] = Order::getOrdersStatus();
        } elseif($action == 'create')
            $this->header = 'اگهی جدید';
        else abort(404);

        $this->mode = $action;

    }
    public function render()
    {
        $this->data['province'] = Setting::getProvince();
        $this->data['city'] = Setting::getCity()[$this->province];
        $this->data['category'] = Category::where('status',Category::AVAILABLE)->where('is_available',1)->pluck('title','id');
        $this->parameters = Parameter::where('category_id',$this->category_id)->where('status','available')->get();
        $category = Category::findOrFail($this->category_id);
        $this->platforms = $category->platform;
        return view('livewire.site.dashboard.orders.store-order');
    }

    public function store()
    {
        if ($this->mode == 'edit')
            $this->saveInDB($this->order);
        else {
            $this->saveInDB(new Order());
            $this->reset(['']);
        }
    }

    public function saveInDB(Order $order)
    {
        if (!in_array($this->order->status,[Order::IS_REQUESTED,Order::IS_FINISHED])) {
            $this->validate(
                [
                    'slug' => ['required', 'string','max:100'],
                    'category_id' => ['required','exists:categories,id'],
                    'content' => ['nullable','string','max:255'],
                    'price' => ['required','numeric', 'between:0,99999999999.99999'],
                    'province' => ['required','string','in:'.implode(',',array_keys($this->data['province']))],
                    'city' => ['required','string','in:'.implode(',',array_keys($this->data['city']))],
                    'image' => ['required','image','mimes:jpg,jpeg,png,svg,gif','max:2048'],
                    'gallery.*' => ['required','image','mimes:jpg,jpeg,png,svg,gif','max:2048'],
                ] , [] , [
                    'slug' => 'عنوان',
                    'category_id' => 'دست بندی',
                    'content' => 'توضیحات',
                    'price' => 'قیمت',
                    'province' => 'استان',
                    'city' => 'شهر',
                    'image' => 'تصویر',
                    'gallery' => 'گالری',
                ]
            );
            $gallery = '';
            foreach ($this->gallery as $image)
                $gallery.= 'storage/'.$image->store('files/orders', 'public').',';

            $order->slug = $this->slug;
            $order->content = $this->content;
            $order->category_id = $this->category_id;
            $order->price = $this->price;
            $order->image = 'storage/'.$this->image->store('files/orders', 'public');
            $order->gallery = rtrim($gallery,',');
            $order->province = $this->province;
            $order->city = $this->city;
            $text = $this->createText('new_order',$order);
            $this->sends($text,$order->user);
            $selectedParameter = [];
            if (!$this->parameters->isEmpty()) {
                if ($this->parameter->isEmpty())
                    return $this->addError('parameters',"ورودی پارامتر ها نامعتبر");
                foreach ($this->parameter as $key => $value) {
                    $parameter = Parameter::find($key);
                    if ($parameter->category_id == $this->category_id) {
                        if (!$parameter->isEmpty()) {
                            if (!empty($value)) {
                                if ($parameter->type == 'number' && is_numeric($value)) {
                                    if (!empty($parameter->max) && $parameter->max < $value)
                                        return $this->addError('parameters',"مقدار ورودی پارامتر $parameter->name بیش ار حد مجاز");
                                    elseif (!empty($parameter->max) && $parameter->min > $value)
                                        return $this->addError('parameters',"مقدار ورودی پارامتر $parameter->name کمتر ار حد مجاز");
                                    else $selectedParameter[] = ['parameter_id'=>$key,'value'=>$value];
                                } elseif($parameter->type == 'text') {
                                    if (!empty($parameter->max) && $parameter->max < strlen($value))
                                        return $this->addError('parameters',"مقدار ورودی پارامتر $parameter->name بیش ار حد مجاز");
                                    elseif (!empty($parameter->max) && $parameter->min > strlen($value))
                                        return $this->addError('parameters',"مقدار ورودی پارامتر $parameter->name کمتر ار حد مجاز");
                                    else $selectedParameter[] = ['parameter_id' => $key , 'value' => $value];
                                }
                            } else return $this->addError('parameters',"ورودی پارامتر $parameter->name نامعتبر");
                        } else return $this->addError('parameters',"ورودی پارامتر ها نامعتبر");
                    }
                }
            }
            $order->parameters()->sync($selectedParameter);
            $selectedPlatform = array_keys(array_filter($this->platform->toArray()));
            $order->platform()->sync($selectedPlatform);
            $order->save();
            $this->resetErrorBag();
            // send success message
        } else {
            // send failed message
        }
    }

}

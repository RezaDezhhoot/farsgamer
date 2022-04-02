<?php

namespace App\Http\Livewire\Site\Dashboard\Orders;

use App\Http\Livewire\BaseComponent;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderParameter;
use App\Models\Parameter;
use App\Models\Setting;
use Carbon\Carbon;
use Livewire\WithFileUploads;

class StoreOrder extends BaseComponent
{
    public $order , $header , $user , $mode , $i , $file = [];
    public $slug , $category_id , $content , $price , $image , $mainImage , $gallery = [] , $galleries = [] , $province , $city ,$result;
    public $parameters , $parameter = [] , $platforms , $platform = [] , $message , $commission , $intermediary;
    public $data = [];
    use WithFileUploads ;
    public function mount($action , $id = null)
    {
        $ban = Carbon::make(now())->diff(\auth()->user()->ban)->format('%r%i');
        if ($ban > 0) {
            $this->addError('error','متاسفانه حساب کابری شما به دلیل نقض قوانین برای مدتی محدود شده است.مدتی بعد دوباره تلاش کنید');
            return;
        }
        if ($action == 'edit') {
            $this->user = auth()->user();
            $this->order = $this->user->orders()->findOrFail($id);
            $this->slug = $this->order->slug;
            $this->content = $this->order->content;
            $this->price = $this->order->price;
            $this->mainImage = $this->order->image;
            $this->gallery = explode(',',$this->order->gallery);
            $this->province = $this->order->province;
            $this->city = $this->order->city;
            $this->result = $this->order->reslut;
            if ($this->order->category->status == Category::AVAILABLE && $this->order->category->is_available == Category::YES) {
                $this->category_id = $this->order->category_id;
                $this->parameter = $this->order->parameters()->pluck('value','parameter_id')->toArray();
                $this->platform = $this->order->platform->pluck('slug', 'id');
            } else
                $this->category_id = '';
            $this->message = $this->order->user->alerts()->where([
                ['subject',Notification::ORDER],
                ['model_id',$this->order->id],
            ])->get();
        } elseif($action == 'create'){
            $this->header = 'اگهی جدید';
        } else abort(404);

        $this->data['province'] = Setting::getProvince();
        $this->data['status'] = Order::getStatus();
        $this->data['subject'] = Notification::getSubject();
        $this->data['category'] = Category::where([
            ['status',Category::AVAILABLE],
            ['is_available',Category::YES]
        ])->pluck('title','id');
        $this->mode = $action;
    }
    public function render()
    {
        if (isset($this->province))
            $this->data['city'] = Setting::getCity()[$this->province];

        if (isset($this->category_id)){
            $this->parameters = Parameter::where('category_id',$this->category_id)->where([
                ['status','available']
            ])->get();
            $category = Category::find($this->category_id);
            $this->platforms = $category->platforms;
        }

        return view('livewire.site.dashboard.orders.store-order');
    }

    public function store()
    {
        if ($this->mode == 'edit')
            $this->saveInDB($this->order);
        else
            $this->saveInDB(new Order());
    }

    public function saveInDB(Order $order)
    {
        if (!in_array($this->order->status,[Order::IS_REQUESTED,Order::IS_FINISHED])) {
            $this->validate(
                [
                    'slug' => ['required', 'string','max:100'],
                    'category_id' => ['required','exists:categories,id'],
                    'content' => ['required','string','max:1200'],
                    'price' => ['required','numeric', 'between:0,999999999999.99999'],
                    'province' => ['required','string','in:'.implode(',',array_keys($this->data['province']))],
                    'city' => ['required','string','in:'.implode(',',array_keys($this->data['city']))],
                    'image' => ['required','image','mimes:'.Setting::getSingleRow('valid_order_images'),'max:'.Setting::getSingleRow('max_order_image_size')],
                    'gallery'=>['array','min:1','max:'.Setting::getSingleRow('order_images_count')],
                    'gallery.*' => ['required','image','mimes:'.Setting::getSingleRow('valid_order_images'),'max:'.Setting::getSingleRow('max_order_image_size')],
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

            $baseAmount = 1000;

            if ($this->price < $baseAmount){
                $baseAmount = number_format($baseAmount);
                return $this->addError('price',"حداقل هزیته برای این اگهی $baseAmount تومان می باشد ");
            }

            if (!is_null($this->image)) {
                @unlink($this->mainImage->image);
                $this->image = 'storage/'.$this->image->store('files/orders', 'public');
                $this->imageWatermark($this->image);
                $order->image = $this->image;
            }

            if (!is_null($this->file)) {
                foreach ($this->file as $image) {
                    $pic = 'storage/'.$image->store('files/orders', 'public').',';
                    $this->imageWatermark($pic,'center');
                    array_push($this->gallery,$pic);
                }
                $order->gallery = implode(',',$this->gallery);
            }

            $order->slug = $this->slug;
            $order->content = $this->content;
            $order->category_id = $this->category_id;
            $order->price = $this->price;
            $order->province = $this->province;
            $order->city = $this->city;
            $order->status = Order::IS_NEW;
            $selectedParameter = [];
            if (!$this->parameters->isEmpty()) {
                foreach ($this->parameters as $key => $value) {
                    if ($value->category_id == $this->category_id) {
                        if (isset($this->parameter[$value->id]) && !empty($this->parameter[$value->id])){
                            if ($value->type == 'number' && is_numeric($this->parameter[$value->id])){
                                if (!empty($value->max) && $value->max < $this->parameter[$value->id])
                                    return $this->addError('parameters',"مقدار ورودی پارامتر $value->name بیش ار حد مجاز");
                                elseif (!empty($value->max) && $value->min > $this->parameter[$value->id])
                                    return $this->addError('parameters',"مقدار ورودی پارامتر $value->name کمتر ار حد مجاز");
                                else $selectedParameter[] = ['parameter_id'=>$value->id,'value'=>$this->parameter[$value->id]];
                            } elseif($value->type == 'text') {
                                if (!empty($value->max) && $value->max < strlen($this->parameter[$value->id]))
                                    return $this->addError('parameters',"مقدار ورودی پارامتر $value->name بیش ار حد مجاز");
                                elseif (!empty($value->max) && $value->min > strlen($this->parameter[$value->id]))
                                    return $this->addError('parameters',"مقدار ورودی پارامتر $value->name کمتر ار حد مجاز");
                                else $selectedParameter[] = ['parameter_id' => $value->id , 'value' => $this->parameter[$value->id]];
                            }
                        } else
                            return $this->addError('parameters',"  پارامتر $value->name الزامی ");
                    }
                }
            }
            OrderParameter::where('order_id',$this->order->id)->delete();
            $order->parameters()->attach($selectedParameter);
            if (!is_null($this->platform)) {
                $selectedPlatform = array_keys(array_filter($this->platform->toArray()));
                $order->platforms()->sync($selectedPlatform);
            }
            $order->save();
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
            $this->resetErrorBag();

            if ($this->mode == 'create')
                return redirect()->route('user.store.order',['edit',$order->id]);
        } else
            $this->emitNotify('برای این اگهی  امکان ویرایش وجود ندارد','warning');
    }

    public function deleteImage($key){
        @unlink($this->gallery[$key]);
        unset($this->gallery[$key]);
    }

    public function deleteImageBeforeSave($key)
    {
        unset($this->file[$key]);
    }

    public function addFile()
    {
        $this->i = $this->i + 1;
        array_push($this->file,$this->i);
    }

}

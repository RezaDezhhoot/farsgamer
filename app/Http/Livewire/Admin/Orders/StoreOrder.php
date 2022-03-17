<?php

namespace App\Http\Livewire\Admin\Orders;

use App\Http\Livewire\BaseComponent;
use App\Models\Notification;
use App\Models\OrderParameter;
use App\Models\Setting;
use App\Traits\Admin\ChatList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Order;
use App\Models\Category;
use App\Models\Parameter;

class StoreOrder extends BaseComponent
{
    use AuthorizesRequests  , ChatList;
    public $mode;
    public $order;
    public $slug , $price , $province , $city , $category , $status , $content , $image , $gallery;
    public $categories , $platforms = [], $platform = [] , $parameters , $parameter = [] , $newMessage , $newMessageStatus;
    public $message;
    public $data;
    public function mount($action , $id)
    {
        $this->authorize('show_orders');
        if ($action <> 'edit')
            abort(404);

        $this->mode = 'edit';
        $this->order = Order::findOrFail($id);
        $this->slug = $this->order->slug;
        $this->price = $this->order->price;
        $this->province = $this->order->province;
        $this->city = $this->order->city;
        if ($this->order->category->status == Category::AVAILABLE && $this->order->category->is_available == Category::YES) {
            $this->category = $this->order->category_id;
            $this->parameter = $this->order->parameters()->pluck('value','parameter_id')->toArray();
            $this->platform = $this->order->platforms->pluck('slug','id');
        }

        $this->status = $this->order->status;
        $this->content = $this->order->content;
        $this->image = $this->order->image;
        $this->gallery = $this->order->gallery;
        $this->message = $this->order->user->alerts()->where([
            ['subject',Notification::ORDER],
            ['model_id',$this->order->id],
        ])->get();
        $this->data['status'] = Order::getStatus();
        $this->data['subject'] = Notification::getSubject();

        $this->chatUserId = $this->order->user->id;
        $this->chats = \auth()->user()->singleContact($this->order->user->id);
        $this->newMessageStatus = Notification::ORDER;
    }

    public function render()
    {
        $this->data['province'] = Setting::getProvince();
        $this->data['city'] = Setting::getCity()[$this->province];
        $this->data['category'] = Category::where([
            ['status',Category::AVAILABLE],
            ['is_available',Category::YES]
        ])->pluck('title','id');
        $this->parameters = Parameter::where('category_id',$this->category)->where('status','available')->get();
        $category = Category::findOrFail($this->category);
        $this->platforms = $category->platforms;
        return view('livewire.admin.orders.store-order' , ['order' => $this->order])
            ->extends('livewire.admin.layouts.admin');
    }

    public function store()
    {
        $this->authorize('edit_orders');
        if ($this->mode == 'edit')
            $this->saveInDB($this->order);
    }

    public function saveInDB(Order $order)
    {
        $this->validate(
            [
                'slug' => ['required', 'string','max:250'],
                'category' => ['required','exists:categories,id'],
                'status' => ['required', 'in:'.Order::IS_NEW.','.Order::IS_UNCONFIRMED.','.Order::IS_CONFIRMED.','.Order::IS_REJECTED.','.Order::IS_REQUESTED],
                'content' => ['nullable','string','max:65000'],
                'price' => ['required','numeric', 'between:0,99999999999.99999'],
                'province' => ['required','string','in:'.implode(',',array_keys($this->data['province']))],
                'city' => ['required','string','in:'.implode(',',array_keys($this->data['city']))],
            ] , [] , [
                'slug' => 'عنوان',
                'category' => 'دست بندی',
                'status' => 'وضعیت',
                'content' => 'توضیحات',
                'price' => 'قیمت',
                'province' => 'استان',
                'city' => 'شهر',
            ]
        );

        if (!in_array($this->order->status , [Order::IS_REQUESTED,Order::IS_FINISHED])) {
            $order->status = $this->status;
            $order->category_id = $this->category;
            $order->price = $this->price;
            $order->province = $this->province;
            $order->city = $this->city;
            $order->slug = $this->slug;
            $order->content = $this->content;
            $selectedParameter = [];
            if (!$this->parameters->isEmpty()) {
                foreach ($this->parameters as $key => $value) {
                    if ($value->category_id == $this->category) {
                        if (isset($this->parameter[$value->id])){
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
                        }
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
        } else
            $this->emitNotify('برای این اگهی  امکان ویرایش وجود ندارد','warning');
        $this->resetErrorBag();

    }


    public function delete()
    {
        $this->authorize('delete_orders');
        if ($this->order->status <> Order::IS_REQUESTED && $this->order->status <> Order::IS_FINISHED )
        {
            $this->order->delete();
            return redirect()->route('admin.order');
        } else {
            $this->emitNotify('برای این سفارش امکان حدف وجود ندارد','warning');
        }
        return(false);
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => ['required','string'],
            'newMessageStatus' => ['required','in:'.implode(',',array_keys(Notification::getSubject()))]
        ],[],[
            'newMessage'=> 'متن',
            'newMessageStatus' => 'وضعیت پیام'
        ]);
        $result = new Notification();
        $result->subject = Notification::ORDER;
        $result->content = $this->newMessage;
        $result->type = Notification::PRIVATE;
        $result->user_id = $this->order->user->id;
        $result->model = Notification::ORDER;
        $result->model_id = $this->order->id;
        $result->save();
        $this->message->push($result);
        $this->reset(['newMessage','newMessageStatus']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

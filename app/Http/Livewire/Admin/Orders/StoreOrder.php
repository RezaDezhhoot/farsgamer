<?php

namespace App\Http\Livewire\Admin\Orders;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\Admin\ChatList;

class StoreOrder extends BaseComponent
{
    use ChatList;
    public $mode;
    public $order;
    public $slug , $price , $province , $city , $category , $status , $content , $image , $gallery;
    public $categories , $platforms = [], $platform = [] , $parameters , $parameter = [] , $newMessage , $newMessageStatus;
    public $message;
    public $data;
    public function mount(
        OrderRepositoryInterface $orderRepository, NotificationRepositoryInterface $notificationRepository , ChatRepositoryInterface $chatRepository,
        CategoryRepositoryInterface $categoryRepository ,
        UserRepositoryInterface $userRepository , $action , $id)
    {
        $this->authorizing('show_orders');
        if ($action <> 'edit')
            abort(404);

        $this->mode = 'edit';
        $this->order = $orderRepository->getOrder($id,false);
        $this->slug = $this->order->slug;
        $this->price = $this->order->price;
        $this->province = $this->order->province;
        $this->city = $this->order->city;
        if ($this->order->category->status == $categoryRepository::availableStatus() && $this->order->category->is_available == $categoryRepository::yes()) {
            $this->category = $this->order->category_id;
            $this->parameter = $this->order->parameters()->pluck('value','parameter_id')->toArray();
            $this->platform = $this->order->platforms->pluck('slug','id');
        }

        $this->status = $this->order->status;
        $this->content = $this->order->content;
        $this->image = $this->order->image;
        $this->gallery = $this->order->gallery;
        $this->message = $userRepository->getUserNotifications($this->order->user,$notificationRepository->orderStatus(),$this->order->id);
        $this->data['status'] = $orderRepository::getStatus();
        $this->data['subject'] = $notificationRepository->getSubjects();

        $this->chatUserId = $this->order->user->id;
        $this->chats = $chatRepository->singleContact($this->order->user->id);
        $this->newMessageStatus = $notificationRepository->orderStatus();
    }

    public function render(SettingRepositoryInterface $settingRepository , CategoryRepositoryInterface $categoryRepository )
    {
        $this->data['province'] = $settingRepository::getProvince();
        $this->data['city'] = $settingRepository->getCity($this->province);
        $this->data['category'] = $categoryRepository->getAll(true,true)->pluck('title','id');
        $category = $categoryRepository->find($this->category,true);
        $this->parameters = $categoryRepository->getParameters($category,true);
        $this->platforms = $category->platforms;
        return view('livewire.admin.orders.store-order' , ['order' => $this->order])
            ->extends('livewire.admin.layouts.admin');
    }

    public function store(OrderRepositoryInterface $orderRepository)
    {
        $this->authorizing('edit_orders');
        if ($this->mode == 'edit')
            $this->saveInDB($orderRepository , $this->order);
    }

    public function saveInDB($orderRepository ,  $order)
    {
        $this->validate(
            [
                'slug' => ['required', 'string','max:250'],
                'category' => ['required','exists:categories,id'],
                'status' => ['required', 'in:'.implode(',',array_keys($orderRepository::getStatus()))],
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

        if (!in_array($this->order->status , [$orderRepository::isRequestedStatus(),$orderRepository::isFinishedStatus()])) {
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

            $orderRepository->deleteParameters($order);
            $orderRepository->attachParameters($order,$selectedParameter);
            if (!is_null($this->platform)) {
                $selectedPlatform = array_keys(array_filter($this->platform->toArray()));
                $orderRepository->syncPlatforms($order,$selectedPlatform);
            }
            $orderRepository->save($order);
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
        } else
            $this->emitNotify('برای این اگهی  امکان ویرایش وجود ندارد','warning');
        $this->resetErrorBag();

    }

    public function delete(OrderRepositoryInterface $orderRepository)
    {
        $this->authorizing('delete_orders');
        if (!in_array($this->order->status,[$orderRepository::isRequestedStatus() ,$orderRepository::isFinishedStatus()]))
        {
            $orderRepository->delete($this->order);
            return redirect()->route('admin.order');
        } else return $this->emitNotify('برای این سفارش امکان حدف وجود ندارد','warning');
    }

    public function sendMessage(NotificationRepositoryInterface $notificationRepository)
    {
        $this->validate([
            'newMessage' => ['required','string','max:255'],
            'newMessageStatus' => ['required','in:'.implode(',',array_keys($notificationRepository->getSubjects()))]
        ],[],[
            'newMessage'=> 'متن',
            'newMessageStatus' => 'وضعیت پیام'
        ]);
        $notification = [
            'subject' => $notificationRepository->orderStatus(),
            'content' =>  $this->newMessage,
            'type' => $notificationRepository->privateType(),
            'user_id' => $this->order->user->id,
            'model' => $notificationRepository->orderStatus(),
            'model_id' => $this->order->id
        ];
        $notification = $notificationRepository->create($notification);
        $this->message->push($notification);
        $this->reset(['newMessage','newMessageStatus']);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }
}

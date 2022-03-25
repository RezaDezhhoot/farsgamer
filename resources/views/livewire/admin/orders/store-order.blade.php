<div>
    <x-admin.chat-panel :chats="$chats" id="kt_chat_modal" />
    <x-admin.form-control deleteAble="true" chatAble="{{true}}"  deleteContent="حذف اگهی" mode="{{$mode}}" title="اگهی"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title"> شناسه اگهی : {{ $order->id }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <div class="row invoice-info">
                <div class="col-xs-12 col-sm-6 invoice-col">
                    <h3>مشخصات کاربر :</h3>
                    <p>
                        <span> نام کامل :</span>
                        <span>{{ $order->user->name }}</span>
                    </p>
                    <p>
                        <span> نام کاربری :</span>
                        <a href="{{route('admin.store.user',$order->user->id)}}">{{ $order->user->user_name }}</a>
                    </p>
                    <p>
                        <span> وضعیت :</span>
                        <span>{{ $order->user->status_label }}</span>
                    </p>
                    <p>
                        <span> شماره هماره :</span>
                        <span>{{ $order->user->phone }}</span>
                    </p>
                    <p>
                        <span> ایمیل :</span>
                        <span>
                        {{ $order->user->email }}
                    </span>
                    </p>
                    <p>
                        <span> استان :</span>
                        <span>
                        {{ $order->user->province_label ?? '' }}
                    </span>
                    </p>
                    <p>
                        <span> شهر :</span>
                        <span>{{ $order->user->city_label ?? '' }}</span>
                    </p>

                </div>
                <div class="col-xs-12 col-sm-6 invoice-col">
                    <h3>مشخصات اگهی :</h3>
                    <p>
                        <span> عنوان :</span>
                        <span>{{ $order->slug }}</span>
                    </p>
                    <p>
                        <span> دسته بندی :</span>
                        <span>{{ $order->category->title }}</span>
                    </p>
                    <p>
                        <span> قیمت :</span>
                        <span>{{ number_format($order->price).' تومان ' }}</span>
                    </p>
                    <p>
                        <span> وضعیت :</span>
                        <span>{{ $order->status_label }}</span>
                    </p>
                    <p>
                        <span> تعداد بازدید :</span>
                        <span>{{ $order->view_count }}  </span>
                    </p>
                    <p>
                        <span> استان :</span>
                        <span>
                        {{ $order->province_label ?? '' }}
                    </span>
                    </p>
                    <p>
                        <span> شهر :</span>
                        <span>{{ $order->city_label }}</span>
                    </p>

                </div>
            </div>
            <x-admin.form-section label="تنظیمات اگهی">
                <x-admin.forms.input type="text" id="slug" label="عنوان*" wire:model.defer="slug"/>
                <x-admin.forms.input type="number" id="price" label="قیمت(تومان)*" wire:model.defer="price"/>
                <x-admin.forms.dropdown id="province" :data="$data['province']" label="استان*" wire:model.defer="province"/>
                <x-admin.forms.dropdown id="city" :data="$data['city']" label="شهر*" wire:model.defer="city"/>
                <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
                <x-admin.forms.lfm-standalone disable="{{true}}" id="image" label="تصویر*" :file="$image" type="image" required="true" wire:model="image"/>
                <x-admin.forms.lfm-standalone disable="{{true}}" id="gallery" label="گالری*" :file="$gallery" type="image" required="true" wire:model="gallery"/>
                <x-admin.forms.full-text-editor id="content" label="توضیحات*" wire:model.defer="content"/>
                <x-admin.forms.dropdown id="category" :data="$data['category']" label="دسته بندی*" wire:model="category"/>
            </x-admin.form-section>
            <hr>
            <x-admin.form-section label="پارامتر های اگهی">
                <div class="row">
                    @foreach($parameters as $key => $item)
                        <div class="col-lg-2 text-center">
                            <img style="border-radius: 50%;width: 50px;height: 50px" src="{{asset($item->logo)}}" alt="">
                            <p>
                                {{ $item->name }}
                            </p>
                            <input class="form-control" type="{{$item->type}}" placeholder="{{ $item->field }}"
                                   {{ !empty($item->max) ? "max=$item->max" : '' }} {{ !empty($item->min) ? "min=$item->min" : '' }}
                                   wire:model.defer="parameter.{{$item->id}}"/>
                            <div class="text-right">
                                @if($item->type == 'number')
                                    @if(!empty($item->min))
                                        <span class="text-info">{حداقل رقم {{$item->min}}}</span>
                                    @endif
                                    @if(!empty($item->max))
                                        <span class="text-info">{حداکثر رقم {{$item->max}}}</span>
                                    @endif
                                @elseif($item->type == 'text')
                                    @if(!empty($item->min))
                                        <span class="text-info">{حداقل کارکتر {{$item->min}}}</span>
                                    @endif
                                    @if(!empty($item->max))
                                        <span class="text-info">{حداکثر کارکتر {{$item->max}}}</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.form-section>
            <hr>
            <x-admin.form-section label="پلتفرم های اگهی">
                <div class="row">
                    @foreach($platforms as $key => $item)
                        <div class="col-2 d-flex align-items-center justify-content-between">
                            <input id="{{ $item->id }}platform" type="checkbox" wire:model.defer="platform.{{$item->id}}"/>
                            <img style="width: 30px;height: 30px;border-radius: 50%" src="{{asset($item->logo)}}" alt="">
                            <label for="{{ $item->id }}platform">
                                {{ $item->slug }}
                            </label>
                            <div style="width: 1px;height: 50px" class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200"></div>
                        </div>
                    @endforeach
                </div>
            </x-admin.form-section>
            <hr>
            <x-admin.form-section label="پیام ها">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>موضوع</th>
                                <th>تاریخ</th>
                                <th style="width: 70%">توضیحات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($message as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->subject_label }}
                                    </td>
                                    <td>
                                        {{$item->date}}
                                    </td>
                                    <td>
                                        {!! $item->content !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-admin.form-section>
            <hr>
            <x-admin.form-section label="پیام جدید">
                <x-admin.forms.validation-errors/>
                <x-admin.forms.basic-text-editor id="editor" label="متن" wire:model.defer="newMessage"/>
                <x-admin.forms.dropdown id="newMessageStatus" :data="$data['subject']" label="اتنخاب موضوع" wire:model.defer="newMessageStatus"/>
                <x-admin.button class="primary" content="ارسال پیام" wire:click="sendMessage()" />
            </x-admin.form-section>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem() {
            Swal.fire({
                title: 'حذف اگهی!',
                text: 'آیا از حذف این اگهی اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                    if (result.isConfirmed) {
                        Swal.fire(
                            'موفیت امیز!',
                            'اگهی مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete')
                }
            })
        }
    </script>
@endpush

<div wire:init="setTimer()">

    <x-admin.chat-panel :chats="$chats" id="kt_chat_modal" />
    <x-admin.form-control mode="{{$mode}}"  title=" معامله {{ $transaction->code }}"/>
    <div class="card card-custom gutter-b example example-compact">
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.form-section label="">
                <div class="row">
                    <div class="col-3">
                        <x-admin.form-section label="زمان باقی مانده">
                            <p  style="padding: 10px;font-size: 20px" id="clock"></p>
                        </x-admin.form-section>
                    </div>
                    <div class="col-7">
                        <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model.defer="status"/>
                    </div>
                    <div class="col-2">
                        <x-admin.form-section label="کد گفتوگو طرفین">
                            {{ $allChats->slug ?? 'بدون صفحه گفتوگو' }}
                        </x-admin.form-section>
                    </div>
                </div>
            </x-admin.form-section>
            <div class="wizard wizard-2" id="kt_wizard" data-wizard-state="step-first" data-wizard-clickable="false">
                <x-admin.wizard-steps>
                    @if(!$transaction->is_returned)
                        @foreach($standardStatus as $key => $item)
                            <x-admin.transaction-step label="{{$item['label']}}" icon="{{$item['icon'] }}" active="{{ $transaction->status == $key ? 'current' : 'pending' }}" desc="{{$item['desc']}}" />
                        @endforeach
                    @else
                        @foreach($returnedStatus as $key => $item)
                            <x-admin.transaction-step label="{{$item['label']}}" icon="{{$item['icon'] }}" active="{{ $transaction->status == $key ? 'current' : 'pending' }}" desc="{{$item['desc']}}" />
                        @endforeach
                    @endif
                </x-admin.wizard-steps>
                <x-admin.wizard-body>
                    <x-admin.form-section label="اطلاعات">
                        @include('livewire.admin.order-transactions.forms')
                    </x-admin.form-section>
                    <div class="form-group">
                        <x-admin.form-section label="وضعیت دریافت توسط خریدار|فروشنده">
                            <p>
                                <x-admin.forms.dropdown id="receivedStatus" :data="$data['received_status']" label="وضعیت" wire:model.defer="receivedStatus"/>
                                <span>توضیحات :</span>
                                {!! $transaction->received_result !!}
                            </p>
                        </x-admin.form-section>
                    </div>
                    @if($transaction->status == $returnStatus)
                        <x-admin.form-section label="مرجوعیت">
                            <x-admin.forms.basic-text-editor id="return_cause" label="علت مرجوعیت*" wire:model.defer="return_cause"/>
                            <x-admin.forms.lfm-standalone id="return_images" disable="{{true}}" label="تصاویر" :file="$return_images" type="image" required="true" wire:model="return_images"/>
                            @if($return == 0)
                                <x-admin.button class="danger" content="انقال معامله به مراحل مرجوعیت" onclick="sendToReturn()" />
                            @elseif($return == 1)
                                <x-admin.button onclick="sendToTransaction()" class="success" content="انقال معامله به مراحل عادی" />
                            @endif
                        </x-admin.form-section>
                    @endif
                    @if($transaction->order->category->type == $physical)
                        <x-admin.form-section label="حمل و نقل">
                            <x-admin.forms.dropdown help="مجاز برای دسته بندی های فیزیکی" id="send_id" :data="$data['transfer']" label="روش ارسال*" wire:model.defer="send_id"/>
                            <x-admin.forms.input help="مجاز برای دسته بندی های فیزیکی"  type="text" id="transfer_result" label="کد رهگیری" wire:model.defer="transfer_result"/>
                        </x-admin.form-section>
                    @endif
                </x-admin.wizard-body>
            </div>
        </div>
        <div class="card-body">
            <x-admin.form-section label="معامله">
                <div class="row">
                    <div class="col-6">
                        <x-admin.form-section label="فروشنده">
                            <div class="table-responsive">
                                <table class="table-bordered table table-striped">
                                    <thead>
                                    <tr>
                                        <th>نام </th>
                                        <th> کاربری</th>
                                        <th>شماره </th>
                                        <th>ایمیل</th>
                                        <th>استان</th>
                                        <th>شهر</th>
                                        <th> پیام</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ $transaction->seller->name }}</td>
                                        <td>{{ $transaction->seller->user_name }}</td>
                                        <td>{{$transaction->seller->phone }}</td>
                                        <td>{{ $transaction->seller->email }}</td>
                                        <td>{{ $transaction->seller->provinceLabel }}</td>
                                        <td>{{$transaction->seller->cityLabel}}</td>
                                        <td>
                                            <div class="btn btn-icon btn-clean btn-lg mr-1" data-toggle="modal" wire:click="setChat('seller')" data-target="#kt_chat_modal">
                                                <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Group-chat.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                                                        <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
                                                    </g>
                                                </svg><!--end::Svg Icon-->
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </x-admin.form-section>
                    </div>
                    <div class="col-6">
                        <x-admin.form-section label="خریدار">
                            <div class="table-responsive">
                                <table class="table-bordered table table-striped">
                                    <thead>
                                    <tr>
                                        <th>نام </th>
                                        <th> کاربری</th>
                                        <th>شماره </th>
                                        <th>ایمیل</th>
                                        <th>استان</th>
                                        <th>شهر</th>
                                        <th> پیام</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ $transaction->customer->name }}</td>
                                        <td>{{ $transaction->customer->user_name }}</td>
                                        <td>{{$transaction->customer->phone }}</td>
                                        <td>{{ $transaction->customer->email }}</td>
                                        <td>{{ $transaction->customer->provinceLabel }}</td>
                                        <td>{{$transaction->customer->cityLabel}}</td>
                                        <td>
                                            <div class="btn btn-icon btn-clean btn-lg mr-1" data-toggle="modal" wire:click="setChat('customer')" data-target="#kt_chat_modal">
                                                <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Group-chat.svg-->
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                                                            <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
                                                        </g>
                                                    </svg><!--end::Svg Icon-->
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </x-admin.form-section>
                    </div>
                    <div class="col-6">
                        <x-admin.form-section label="اگهی">
                            <div class="table-responsive">
                                <table class="table-bordered table table-striped">
                                    <thead>
                                    <tr>
                                        <th>عنوان </th>
                                        <th> دسته بندی</th>
                                        <th>قیمت </th>
                                        <th>کارمزد</th>
                                        <th>حق واسطه گری</th>
                                        <th>استان</th>
                                        <th>شهر</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ $transaction->order->slug }}</td>
                                        <td>{{ $transaction->order->category->title }}</td>
                                        <td>{{ number_format($transaction->order->price).' تومان ' }}</td>
                                        <td>{{ number_format($transaction->commission).' تومان ' }}</td>
                                        <td>{{ number_format($transaction->intermediary).' تومان ' }}</td>
                                        <td>{{ $transaction->order->provinceLabel }}</td>
                                        <td>{{ $transaction->order->cityLabel }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </x-admin.form-section>
                    </div>
                    <div class="col-6">
                        <x-admin.form-section label="پرداخت">
                            <div class="table-responsive">
                                <table class="table-bordered table table-striped">
                                    <thead>
                                    <tr>
                                        <th>پرداخت کننده </th>
                                        <th>مبلغ </th>
                                        <th>وضعیت</th>
                                        <th>درگاه</th>
                                        <th>تاریخ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ $transaction->payment->user->name ?? '' }}</td>
                                        <td>{{ number_format($transaction->payment->price ?? 0).' تومان ' }}</td>
                                        <td>{{ $transaction->payment->statusLabel ?? '' }}</td>
                                        <td>{{ $transaction->payment->gateway ?? '' }}</td>
                                        <td>{{ $transaction->payment->date ?? '' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </x-admin.form-section>
                    </div>
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
                                <th>توضیحات</th>
                                <th>مخاطب</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($message as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->subjectLabel }}
                                    </td>
                                    <td>
                                        {{$item->date}}
                                    </td>
                                    <td>
                                        {!! $item->content !!}
                                    </td>
                                    <td>
                                        {{ $item->user->name }}({{$data['send'][$item->user->id]}})
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-admin.form-section>
            <hr>
            <x-admin.form-section label="ارسال پیام">
                <x-admin.forms.basic-text-editor id="editor" label="پیام جدید*" wire:model.defer="newMessage"/>
                <x-admin.forms.dropdown id="newMessageStatus" :data="$data['send']" label="ارسال برای*" wire:model.defer="newMessageStatus"/>
                <x-admin.forms.dropdown id="newMessageSubject" :data="$data['messageSubject']" label="موضوع*" wire:model.defer="newMessageSubject"/>
                <x-admin.button class="primary" content="ارسال" wire:click="sendNewMessage()" />
            </x-admin.form-section>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        Livewire.on('timer', function (data) {
            $('#clock').countdown(data.data)
                .on('update.countdown', function(event) {
                    var format = '%H:%M:%S';
                    if(event.offset.totalDays > 0) {
                        format = '%-d روز ' + format;
                    }
                    if(event.offset.weeks > 0) {
                        format = '%-w هفته ' + format;
                    }
                    $(this).html(event.strftime(format));
                })
                .on('finish.countdown', function(event) {
                    $(this).html('اتمام زمان!')
                        .parent().addClass('disabled');

                });
        })

        function cancelTransaction() {

        }

        function sendToReturn() {
            Swal.fire({
                title: 'انتقال معامله!',
                text: 'آیا از انتقال این معامله به مراحل مرجوعیت اطمینان دارید؟',
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
                        )
                    }
                @this.call('sendToReturn')
                }
            })
        }

    </script>
@endpush

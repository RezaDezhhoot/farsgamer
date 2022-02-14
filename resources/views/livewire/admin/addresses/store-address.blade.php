<div>
    <x-admin.chat-panel :chats="$chats" id="kt_chat_modal"  />
    <x-admin.form-control  deleteAble="true" chatAble="{{true}}"  deleteContent="حذف ادرس" mode="{{$mode}}" title="ادرس"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="country" disabled label="کشور" wire:model.defer="country"/>
            <x-admin.forms.dropdown disabled id="province" :data="$data['province']" label="استان*" wire:model="province"/>
            <x-admin.forms.dropdown disabled id="city" :data="$data['city']" label="استان*" wire:model="city"/>
            <x-admin.forms.text-area disabled label="ادرس" id="addressText" wire:model.defer="addressText" />
            <x-admin.forms.input disabled type="text" id="postal_code"  label="کد پستی*" wire:model.defer="postal_code"/>
            <x-admin.forms.input disabled type="text" id="first_name"  label="نام*" wire:model.defer="first_name"/>
            <x-admin.forms.input disabled type="text" id="last_name"  label="نام خانوادگی*" wire:model.defer="last_name"/>
            <x-admin.forms.input disabled type="text" id="phone"  label="شماره همراه*" wire:model.defer="phone"/>
            <x-admin.forms.input disabled type="email" id="email"  label="ادرس ایمیل*" wire:model.defer="email"/>
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model="status"/>
            <hr>
            <x-admin.form-section label="پیام ها">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>موضوع</th>
                                <th>تاریخ</th>
                                <th style="width: 70%">توضیحات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($message as $item)
                                <tr>
                                    <td>
                                        {{ $item->subjectLabel }}
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
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف ادرس!',
                text: 'آیا از حذف این ادرس اطمینان دارید؟',
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
                            'ادرس مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

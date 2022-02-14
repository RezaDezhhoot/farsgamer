<div>
    <x-admin.chat-panel :chats="$chats" id="kt_chat_modal"  />
    <x-admin.form-control deleteAble="true"  chatAble="{{true}}" deleteContent="حذف درخواست" mode="{{$mode}}" title="درخواست"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" disabled id="user" label="کاربر" wire:model.defer="user"/>
            <x-admin.forms.input type="text" disabled id="phone" label="شماره همراه" wire:model.defer="phone"/>
            <x-admin.forms.input type="text" disabled id="card" label="شماره کارت" wire:model.defer="card"/>
            <x-admin.forms.input type="text" disabled id="sheba" label="شماره شبا" wire:model.defer="sheba"/>
            <x-admin.forms.input type="text" disabled id="bank" label="بانک" wire:model.defer="bank"/>
            <x-admin.forms.input type="text" disabled id="price" label="مبلغ(تومان)" wire:model.defer="price"/>
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
            <x-admin.forms.full-text-editor id="result" label="توضیحات" wire:model.defer="result"/>
            <x-admin.forms.lfm-standalone id="file" label="تصویر رسید" :file="$file" type="image" required="true" wire:model="file"/>
            <x-admin.forms.input type="url" id="link" label="لینک پیگیری" wire:model.defer="link"/>
            <x-admin.forms.input type="number" id="track_id" label="شماره پیگیری" wire:model.defer="track_id"/>
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
        function deleteItem() {
            Swal.fire({
                title: 'حذف درخواست!',
                text: 'آیا از درخواست این اگهی اطمینان دارید؟',
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
                            'درخواست مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete')
                }
            })
        }
    </script>
@endpush

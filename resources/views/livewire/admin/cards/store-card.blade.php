<div>
    <x-admin.chat-panel :chats="$chats" id="kt_chat_modal"  />
    <x-admin.form-control deleteAble="true"  chatAble="{{true}}" deleteContent="حذف کارت" mode="{{$mode}}" title="کارت"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title"><a target="_blank" href="{{route('admin.store.user',['edit',$card->user->id])}}">{{ $header }}</a></h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="number" id="card_number" label="شماره کارت*" wire:model.defer="card_number"/>
            <x-admin.forms.input type="text" id="card_sheba" help="همراه با IR" label="شماره شبا*" wire:model.defer="card_sheba"/>
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
            <x-admin.forms.dropdown id="bank" :data="$data['bank']" label="بانک*" wire:model.defer="bank"/>
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
                title: 'حذف کارت!',
                text: 'آیا از حذف این کارت اطمینان دارید؟',
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
                            'کارت مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

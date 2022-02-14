<div>
    <x-admin.chat-panel :chats="$chats" id="kt_chat_modal" />
    <x-admin.form-control deleteAble="true" chatAble="{{true}}" deleteContent="حذف تیکت" mode="{{$mode}}" title="تیکت" />
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.dropdown id="subject" value="true" :data="$data['subject']" label="موضوع*" wire:model.defer="subject"/>
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
            <x-admin.forms.dropdown id="priority"  :data="$data['priority']" label="الویت*" wire:model.defer="priority"/>
            <x-admin.forms.select2 id="kt_select2_1" :data="$data['user']"  label="کاربر*" wire:model.defer="user_id"/>
            <x-admin.forms.full-text-editor id="content" label="متن اصلی*" wire:model.defer="content"/>
            <x-admin.forms.lfm-standalone id="file" label="فایل" :file="$file" type="image" required="true" wire:model="file"/>
            @if($mode == 'edit')
                <x-admin.form-section label="تاریخپه">
                    @foreach($child as $key =>  $item)
                        <div class="col-lg-12 border" style="border: 1px gray solid;padding: 5px;border-radius: 5px;margin: 10px">
                            <h5>
                                {{ $item->user->fullName }}:
                            </h5>
                            <p>
                                {!! $item->content !!}
                                {{ $item->date }}
                            </p>
                            <p>
                                <label for="">فایل</label>
                                <a class="btn btn-link" href="{{ $item->file }}">{{ $item->file }}</a>
                            </p>
                            <button class="btn btn-danger" wire:click="delete({{$key}})">حذف</button>
                        </div>
                    @endforeach
                </x-admin.form-section>
                <x-admin.form-section label="ارسال پاسخ">
                    <x-admin.forms.full-text-editor id="answer" label="" wire:model.defer="answer"/>
                    <x-admin.forms.input type="text"  id="answerFile" label="فایل" wire:model.defer="answerFile"/>
                    <x-admin.button class="primary" content="ثبت" wire:click="newAnswer()" />
                </x-admin.form-section>
            @endif
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف تیکت  !',
                text: 'آیا از حذف این تیکت   اطمینان دارید؟',
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
                            'تیکت   مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

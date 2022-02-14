<div>
    <x-admin.chat-panel :chats="$chats" id="kt_chat_modal" />
    <x-admin.form-control deleteAble="true"  chatAble="{{true}}" deleteContent="حذف کامنت" mode="{{$mode}}" title="کامنت"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title"><a target="_blank" href="{{route('admin.store.user',['edit',$comment->user->id])}}">{{ $header }}</a></h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.dropdown id="type" disabled :data="$data['type']" label="نوع" wire:model.defer="type"/>
            <x-admin.forms.input type="text" disabled id="case" label="مورد" wire:model.defer="case"/>
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
            <x-admin.forms.text-area label="متن*"  wire:model.defer="content" id="content" />
            <x-admin.forms.input type="number" id="score" label="امتیاز*" wire:model.defer="score"/>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف کامنت!',
                text: 'آیا از حذف این کامنت اطمینان دارید؟',
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
                            'کامنت مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

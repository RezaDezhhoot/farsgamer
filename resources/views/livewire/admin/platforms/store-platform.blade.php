<div>
    <x-admin.form-control deleteAble="true" deleteContent="حذف پلتفرم" mode="{{$mode}}" title="پلتفرم"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="slug" label="نام مستعار*" wire:model.defer="slug"/>
            <x-admin.forms.lfm-standalone id="logo" label="ایکون*" :file="$logo" type="image" required="true" wire:model="logo"/>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف پلتفرم!',
                text: 'آیا از حذف این پلتفرم اطمینان دارید؟',
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
                            'پلتفرم مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

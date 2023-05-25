<div>
    <x-admin.form-control deleteAble="true" deleteContent="حذف قانون" mode="{{$mode}}" title=" قانون"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="title" label="عنوان*" wire:model.defer="title"/>
            <x-admin.forms.full-text-editor id="content" label="قانون*" wire:model.defer="content"/>
            <x-admin.forms.input type="number" id="order" label="نمایش*" wire:model.defer="order"/>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف قانون!',
                text: 'آیا از حذف این قانون اطمینان دارید؟',
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
                            'قانون مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

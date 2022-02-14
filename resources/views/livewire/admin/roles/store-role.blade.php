<div>
    <x-admin.form-control deleteAble="true" deleteContent="حذف نقش" mode="{{$mode}}" title="نقش"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="name" label="عنوان*" wire:model.defer="name"/>
            <x-admin.form-section label="دسترسی ها">
                <div class="row">
                    @foreach($permission as $key => $item)
                        <div class="col-lg-2">
                            <x-admin.forms.checkbox label="{{$item['label']}}" value="{{$item['name']}}" id="permissions-{{$item['name']}}"
                                                    wire:model.defer="permissionSelected" />
                        </div>
                    @endforeach
                </div>
            </x-admin.form-section>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف نقش  !',
                text: 'آیا از حذف این نقش اطمینان دارید؟',
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
                            'نقش  مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

<div>
    <x-admin.form-control deleteAble="true" deleteContent="حذف سوال" mode="{{$mode}}" title=" سوال"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.full-text-editor id="question" label="سوال*" wire:model.defer="question"/>
            <x-admin.forms.full-text-editor id="answer" label="جواب*" wire:model.defer="answer"/>
            <x-admin.forms.input type="text" id="category" label="دسته*" wire:model.defer="category"/>
            <x-admin.forms.input type="number" id="order" label="نمایش*" wire:model.defer="order"/>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف سوال!',
                text: 'آیا از سوال این قانون اطمینان دارید؟',
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
                            'سوال مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

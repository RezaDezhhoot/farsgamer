<div>
    <x-admin.form-control deleteAble="true" deleteContent="حذف روش ارسال" mode="{{$mode}}" title="نقل و انتقال"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="slug" label="نام مستعار*" wire:model.defer="slug"/>
            <x-admin.forms.lfm-standalone id="logo" label="ایکون*" :file="$logo" type="image" required="true" wire:model="logo"/>
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
            <x-admin.forms.input type="number" id="send_time_inner_city" help="بر حسب دقیقه" label="حداکثر زمان برای ارسال درون شهری*" wire:model.defer="send_time_inner_city"/>
            <x-admin.forms.input type="number" id="send_time_inner_city" help="بر حسب دقیقه" label="حداکثر زمان برای ارسال بین شهری*" wire:model.defer="send_time_outer_city"/>
            <x-admin.forms.full-text-editor id="note" label="توضیحات" wire:model.defer="note"/>
            <x-admin.forms.checkbox id="pursuit" label="قابل پیگیری" wire:model.defer="pursuit" />
            <x-admin.forms.input type="url" id="pursuit_web_site" label="سایت مورد نظر برای پیگیری" wire:model.defer="pursuit_web_site"/>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف روش ارسال!',
                text: 'آیا از حذف این روش ارسال اطمینان دارید؟',
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
                            'روش ارسال مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

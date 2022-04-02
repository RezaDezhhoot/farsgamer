<div>
    <x-admin.form-control deleteAble="true" deleteContent="حذف مقاله" mode="{{$mode}}" title="مقاله" />
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="slug" label="نام مستعار*" wire:model.defer="slug"/>
            <x-admin.forms.input type="text" id="title" label="عنوان*" wire:model.defer="title"/>
            <x-admin.forms.lfm-standalone id="main_image" label="تصویر*" :file="$main_image" type="image" required="true" wire:model="main_image"/>
            <x-admin.forms.full-text-editor id="content" label="محتوا*" wire:model.defer="content"/>
            <x-admin.forms.text-area label="کلمات کلیدی*" help="کلمات را با کاما از هم جدا کنید" wire:model.defer="seo_keywords" id="seo_keywords" />
            <x-admin.forms.text-area label="توضیحات سئو*" wire:model.defer="seo_description" id="seo_description" />
            <x-admin.forms.input type="number" id="score" label="امتیاز*" wire:model.defer="score"/>
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
            <x-admin.forms.checkbox label="قابل کامنت گذاری" id="commentable" wire:model.defer="commentable" />
            <x-admin.forms.checkbox label="شناسایی به موتور های جستوجو" id="google_indexing" wire:model.defer="google_indexing" />
            <hr>
            <x-admin.form-section label="دسته ها">
                <div class="row" style="display: flex">
                    @foreach($data['category'] as $key => $item)
                        <div class="col-lg-2">
                            <x-admin.forms.checkbox label="{{ $item['title'] }}" value="{{ $item['id'] }}" id="{{ $item['id'] }}category"
                                                    wire:model.defer="categories" />
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
                title: 'حذف مقاله!',
                text: 'آیا از حذف این مقاله اطمینان دارید؟',
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
                            'مقاله مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
    </script>
@endpush

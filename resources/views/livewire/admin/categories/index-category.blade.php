<div>
    <x-admin.form-control link="{{ route('admin.store.category',['create'] ) }}" title="دسته بندی ها"/>
    <div class="card card-custom">
        <div class="card-body">
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model="status"/>
            <x-admin.forms.dropdown id="is_available" :data="$data['is_available']" label="نوع دسته" wire:model="is_available"/>
            <x-admin.forms.dropdown id="type" :data="$data['type']" label="نوع محصولات" wire:model="type"/>
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th> ایکون</th>
                            <th>نام مستعار</th>
                            <th>عنوان</th>
                            <th>وضعیت</th>
                            <th>دسته مادر</th>
                            <th>نوع دسته</th>
                            <th>نوع محصولات</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($categories as $item)
                            <tr>
                                <td><img style="width: 30px;height: 30px" src="{{ asset($item->logo) }}" alt=""></td>
                                <td>{{ $item->slug }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->status_label }}</td>
                                <td>{{ $item->parent->title ?? '' }}</td>
                                <td>{{ $item->available_label }}</td>
                                <td>{{ $item->type_label }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.category',['edit', $item->id]) }}" />
                                    <x-admin.delete-btn onclick="deleteItem({{$item->id}})" />
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="8">
                                دیتایی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$categories->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف دسته بندی!',
                text: 'آیا از حذف این دسته بندی اطمینان دارید؟',
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
                            'دسته بندی مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

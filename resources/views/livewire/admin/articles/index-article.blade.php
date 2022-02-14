<div>
    <x-admin.form-control link="{{ route('admin.store.article',['create'] ) }}"  title="مقالات"/>
    <div class="card card-custom">
        <div class="card-body">
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model="status"/>
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table  class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>نام مستعار</th>
                            <th>عنوان</th>
                            <th>وضعیت</th>
                            <th>دسته ها</th>
                            <th>نویسنده</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($articles as $item)
                            <tr>
                                <td>{{ $item->slug }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item::getStatus()[$item->status] }}</td>
                                <td>
                                    @foreach($item->categories->pluck('slug','id') as $value)
                                        <span class="badge-info">
                                            {{ $value }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>{{ $item->author->fullName }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.article',['edit', $item->id]) }}" />
                                    <x-admin.delete-btn onclick="deleteItem({{$item->id}})" />
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="6">
                                دیتایی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$articles->links('livewire.admin.layouts.paginate')}}
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
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

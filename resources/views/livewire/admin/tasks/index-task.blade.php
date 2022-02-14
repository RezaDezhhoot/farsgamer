<div>
    <x-admin.form-control link="{{ route('admin.store.task',['create'] ) }}" title="وظایف"/>
    <div class="card card-custom">
        <div class="card-body">
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table  class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>عنوان</th>
                            <th>رویداد</th>
                            <th>شرط</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($tasks as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item::event()[$item->task] }}</td>
                                <td>{{ $item::tasks()[$item->where] }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.task',['edit', $item->id]) }}" />
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
            {{$tasks->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف وظیفه!',
                text: 'آیا از حذف این وظیفه اطمینان دارید؟',
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
                            'وظیفه مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

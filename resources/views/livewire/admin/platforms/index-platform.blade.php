<div>
    <x-admin.form-control link="{{ route('admin.store.platform',['create'] ) }}" title="پلتفرم ها"/>
    <div class="card card-custom">
        <div class="card-body">
            <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                @include('livewire.admin.layouts.advance-table')
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-striped" id="kt_datatable">
                            <thead>
                            <tr>
                                <th> ایکون</th>
                                <th>نام مستعار</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($platforms as $item)
                                <tr>
                                    <td><img style="width: 30px;height: 30px" src="{{ asset($item->logo) }}" alt=""></td>
                                    <td>{{ $item->slug }}</td>
                                    <td>
                                        <x-admin.edit-btn href="{{ route('admin.store.platform',['edit', $item->id]) }}" />
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
            </div>
            {{$platforms->links('livewire.admin.layouts.paginate')}}
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
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

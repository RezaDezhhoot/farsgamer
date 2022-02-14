<div>
    <x-admin.form-control link="{{ route('admin.setting.fag.create',['create'] ) }}" title="{{ $header }}"/>
    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table  class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>نمایش</th>
                            <th> سوال</th>
                            <th>جواب</th>
                            <th>دسته</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($questions as $item)
                            <tr>
                                <td>{{ $item['value']['order'] }}</td>
                                <td>{!!  $item['value']['question'] !!}</td>
                                <td>{!! $item['value']['answer'] !!}</td>
                                <td>{{ $item['value']['category'] }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.setting.fag.create',['edit', $item['id']]) }}" />
                                    <x-admin.delete-btn onclick="deleteItem({{$item['id']}})" />
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
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف سوال!',
                text: 'آیا از حذف این سوال اطمینان دارید؟',
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
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

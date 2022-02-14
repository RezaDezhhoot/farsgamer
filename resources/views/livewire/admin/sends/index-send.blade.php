<div>
    <x-admin.form-control link="{{ route('admin.store.send',['create'] ) }}" title="نقل و انتقال"/>
    <div class="card card-custom">
        <div class="card-body">
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model="status"/>
            <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                @include('livewire.admin.layouts.advance-table')
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-striped" id="kt_datatable">
                            <thead>
                            <tr>
                                <th> ایکون</th>
                                <th>نام مستعار</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sends as $item)
                                <tr>
                                    <td><img style="width: 30px;height: 30px" src="{{ asset($item->logo) }}" alt=""></td>
                                    <td>{{ $item->slug }}</td>
                                    <td>{{ $item::getStatus()[$item->status] }}</td>
                                    <td>
                                        <x-admin.edit-btn href="{{ route('admin.store.send',['edit', $item->id]) }}" />
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
                {{$sends->links('livewire.admin.layouts.paginate')}}
            </div>
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
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

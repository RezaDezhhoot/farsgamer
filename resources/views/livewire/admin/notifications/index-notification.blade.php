<div>
    <x-admin.form-control link="{{ route('admin.store.notification',['create'] ) }}" title="اعلان ها"/>
    <div class="card card-custom">
        <div class="card-body">
            <x-admin.forms.dropdown id="type" :data="$data['type']" label="نوع اعلان" wire:model="type"/>
            <x-admin.forms.dropdown id="subject" :data="$data['subject']" label="موضوع" wire:model="subject"/>
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table  class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>شماره</th>
                            <th>موضوع</th>
                            <th>نام کاربری</th>
                            <th>نوع</th>
                            <th>متن</th>
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($notification as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->subjectLabel }}</td>
                                <td>{{ $item->user->user_name ?? 'عمومی' }}</td>
                                <td>{{ $item::getType()[$item->type] }}</td>
                                <td style="width: 40%;">{!! $item->content !!}</td>
                                <td>{{ $item->date }}</td>
                                <td>
                                    <x-admin.delete-btn onclick="deleteItem({{$item->id}})" />
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="7">
                                دیتایی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$notification->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف اعلان!',
                text: 'آیا از حذف این اعلان اطمینان دارید؟',
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
                            'اعلان مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

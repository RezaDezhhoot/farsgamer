<div>
    <x-admin.form-control store="{{false}}"  title="ادرس ها"/>
    <div class="card card-custom">
        <div class="card-body">
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model="status"/>
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th> کشور</th>
                            <th>استان</th>
                            <th>شهر</th>
                            <th>کد پستی</th>
                            <th>کاربر</th>
                            <th>شماره همراه</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($addresses as $item)
                            <tr>
                                <td>{{ $item->country }}</td>
                                <td>{{ \App\Models\Setting::getProvince()[$item->province] }}</td>
                                <td>{{ \App\Models\Setting::getCity()[$item->province][$item->city] }}</td>
                                <td>{{ $item->postal_code }}</td>
                                <td>{{ $item->name }}</td>
                                <td> {{ $item->phone }} </td>
                                <td> {{ $item::getStatus()[$item->status] }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.address',['edit', $item->id]) }}" />
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
            {{$addresses->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف ادرس!',
                text: 'آیا از حذف این ادرس اطمینان دارید؟',
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
                            'ادرس مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

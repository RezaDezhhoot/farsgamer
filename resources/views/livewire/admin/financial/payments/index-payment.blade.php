<div>
    <x-admin.form-control store="{{false}}" title="رسید های پرداخت"/>
    <div class="card card-custom">
        <div class="card-body">
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model="status"/>
            <x-admin.forms.input type="text" id="user" label="نام کاربری یا شماره همراه کاربر" wire:model="user" />
            <x-admin.forms.input type="text" id="ip" label="ای پی پرداخت کننده" wire:model="ip" />
            @include('livewire.admin.layouts.advance-table')
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره رسید</th>
                            <th>پرداخت کننده</th>
                            <th>ای پی</th>
                            <th>وضعیت</th>
                            <th>نتیجه</th>
                            <th>مبلع(تومان)</th>
                            <th>درگاه</th>
                            <th>کد پیگیری</th>
                            <th>پیام<th>
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($payments as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->user->fullName }}</td>
                                <td>{{ $item->user->amount }}</td>
                                <td>{{ $item->status_code }}</td>
                                <td>{{ $item->payment_ref ? 'تایید شده' : 'تایید نشده' }}</td>
                                <td>{{ number_format($item->price) }}</td>
                                <td>{{ $item->payment_gateway }}</td>
                                <td>{{ $item->payment_token }}</td>
                                <td>{{ $item->status_message }}</td>
                                <td>{{ $item->date }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.payment',['edit', $item->id]) }}" />
                                    <x-admin.delete-btn onclick="deleteItem({{$item->id}})" />
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="11">
                                دیتایی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$payments->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف رسید!',
                text: 'آیا از حذف این رسید اطمینان دارید؟',
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
                            'رسید مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

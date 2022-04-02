<div>
    <x-admin.form-control store="{{false}}" title="درخواست ها"/>
    <div class="card card-custom">
        <div class="card-body">
           <div class="row">
               <div class="col-12 py-1">
                   @foreach($data['status'] as $key => $item)
                       <button class="btn btn-link" wire:click="$set('status','{{$key}}')">{{$item}}</button>
                   @endforeach
               </div>
           </div>
            <x-admin.forms.input type="text" label="شماره همراه کاربر" id="phone" wire:model="phone" />
            <x-admin.forms.input type="text" label="نام کاربری کاربر" id="user_name" wire:model="user_name" />
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره درخواست</th>
                            <th>کد پیگیری</th>
                            <th>کاربر</th>
                            <th>مبلغ(تومان)</th>
                            <th>بانک</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($requests as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->track_id ?? '' }}</td>
                                <td>{{ $item->user->fullName }}</td>
                                <td>{{ number_format($item->price) }}</td>
                                <td>{{ $item->card->bank_label }}</td>
                                <td>{{ $item->status_label }}</td>
                                <td>{{ $item->date }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.request',['edit', $item->id]) }}" />
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="9">
                                دیتایی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$requests->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف درخواست!',
                text: 'آیا از حذف این درخواست اطمینان دارید؟',
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
                            'درخواست مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

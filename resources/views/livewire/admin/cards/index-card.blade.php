<div>
    <x-admin.form-control store="{{false}}" title="حساب های بانکی"/>
    <div class="card card-custom">
        <div class="card-body">
           <div class="row">
              <div class="col-12 py-1">
                  @foreach($data['status'] as $key => $item)
                      <button class="btn btn-link" wire:click="$set('status','{{$key}}')">{{$item}}</button>
                  @endforeach
              </div>
           </div>
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>کاربر</th>
                            <th>شماره کارت</th>
                            <th>شبا</th>
                            <th>بانک</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($cards as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->user->fullName }}</td>
                                <td>{{ $item->card_number }}</td>
                                <td>{{ $item->card_sheba }}</td>
                                <td>{{ $item->bank_label }}</td>
                                <td>{{ $item->status_label }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.card',['edit', $item->id]) }}" />
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
            {{$cards->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف حساب!',
                text: 'آیا از حذف این حساب اطمینان دارید؟',
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
                            'حساب مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

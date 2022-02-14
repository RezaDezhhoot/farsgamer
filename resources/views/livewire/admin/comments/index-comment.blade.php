<div>
    <x-admin.form-control store="{{false}}" title="کامنت ها"/>
    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <div class="col-12 py-1">
                    @foreach($data['status'] as $key => $item)
                        <button class="btn btn-link" wire:click="$set('status','{{$key}}')">{{ $item }}</button>
                    @endforeach
                </div>
            </div>
            <x-admin.forms.dropdown id="for" :data="$data['for']" label="نوع" wire:model="for"/>
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>کاربر</th>
                            <th>وضعیت</th>
                            <th>نوع</th>
                            <th>مورد</th>
                            <th>امتیاز</th>
                            <th>متن</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($comments as $item)
                            <tr>
                                <td>{{ $item->user->fullName ?? $item->user->user_name }}</td>
                                <td>{{ $item::getStatus()[$item->status] }}</td>
                                <td>{{ $item::getFor()[$item->type] }}</td>
                                <td>{{ $item->target }}</td>
                                <td>{{ $item->score }}</td>
                                <td style="width: 40%;">{!!  $item->content !!}</td>
                                <td>
                                    <x-admin.ok-btn wire:click="confirm({{$item->id}})" />
                                    <x-admin.edit-btn href="{{ route('admin.store.comment',['edit', $item->id]) }}" />
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

            {{$comments->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف کامنت!',
                text: 'آیا از حذف این کامنت اطمینان دارید؟',
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
                            'کامنت مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

<div>
    <x-admin.form-control store="{{false}}" title="اگهی ها"/>
    <div class="card card-custom">
        <div class="card-body">
            <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    <button  wire:click="$set('status', '')" class="btn btn-link"  title="">
                        همه
                        ({{$statusCount['all']}})
                    </button>
                    @foreach($statusCount as $key => $item)
                        @if($loop->first)
                            @continue
                        @endif
                        @if($status == $key)
                            <button  type="button"  wire:click="$set('status', '{{$key}}')" class="btn btn-link disabled"  title="">
                                {{ $item }}
                            </button>
                        @else
                            <button   wire:click="$set('status', '{{$key}}')" class="btn btn-link" title="">
                                {{$item}}
                            </button>
                        @endif
                    @endforeach
                </div>
                <x-admin.forms.dropdown id="category" :data="$data['category']" label="دسته بندی" wire:model="category"/>
                @include('livewire.admin.layouts.advance-table')
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-striped" id="kt_datatable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد اگهی</th>
                                <th>عنوان</th>
                                <th> کاربر</th>
                                <th>دسته بندی</th>
                                <th>وضعیت</th>
                                <th>قیمت (تومان)</th>
                                <th>تاریخ</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->slug }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->category->slug }}</td>
                                    <td>
                                        <b>
                                            {{ $item->statusLabel }}
                                        </b>
                                    </td>
                                    <td>{{ number_format($item->price)  }}</td>
                                    <td>{{ $item->date }}</td>
                                    <td>
                                        <x-admin.edit-btn href="{{ route('admin.store.order',['edit', $item->id]) }}" />
                                        <x-admin.delete-btn onclick="deleteItem({{$item->id}})" />
                                    </td>
                                </tr>
                            @empty
                                <td class="text-center" colspan="10">
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
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف مقاله!',
                text: 'آیا از حذف این اگهی اطمینان دارید؟',
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
                            'اگهی مورد نظر با موفقیت حذف شد',
                        )
                    }
                    @this.call('delete', id)
                }
            })
        }
    </script>
@endpush

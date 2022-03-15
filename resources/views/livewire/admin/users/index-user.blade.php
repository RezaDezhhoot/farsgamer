<div>
    <x-admin.form-control link="{{ route('admin.store.user',['create'] ) }}" title="کاربران"/>
    <div class="card card-custom">
        <div class="card-body">
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model="status"/>
            <x-admin.forms.dropdown id="status" :data="$data['roles']" label="نقش" wire:model="roles"/>
            <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                @include('livewire.admin.layouts.advance-table')
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-striped" id="kt_datatable">
                            <thead>
                            <tr>
                                <th>شماره</th>
                                <th> نام</th>
                                <th>شماره همراه</th>
                                <th>موجودی کیف پول(تومان)</th>
                                <th>اگهی ها</th>
                                <th>نام کاربری</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ number_format( $item->balance)  }}</td>
                                    <td>{{ $item->orders()->count() }}</td>
                                    <td>{{ $item->user_name }}</td>
                                    <td>{{ $item::getStatus()[$item->status] }}</td>
                                    <td>
                                        <x-admin.edit-btn href="{{ route('admin.store.user',['edit', $item->id]) }}" />
                                        <x-admin.ok-btn wire:click="confirm({{$item->id}})" />
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
            </div>
            {{$users->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>

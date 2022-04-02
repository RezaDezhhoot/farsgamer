<div>
    <x-admin.form-control link="{{ route('admin.store.role',['create'] ) }}" title="نقش ها"/>
    <div class="card card-custom">
        <div class="card-body">
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table  class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره</th>
                            <th> عنوان</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($roles as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <x-admin.edit-btn href="{{ route('admin.store.role',['edit', $item->id]) }}" />
                                </td>
                            </tr>
                        @empty
                                <td class="text-center" colspan="4">
                                دیتایی جهت نمایش وجود ندارد
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$roles->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>

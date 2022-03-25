<div>
    <x-admin.form-control title="گزارش ها"/>
    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table  class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>موضوع</th>
                            <th> اکشن</th>
                            <th>وضعیت مورد</th>
                            <th>کاربر</th>
                            <th>وضعیت </th>
                            <th>انجام دهنده</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($reports as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->subject }}</td>
                                <td>{{ $item->action }}</td>
                                <td>{{ $item->row_status }}</td>
                                <td>{{ $item->user->user_name  }}</td>
                                <td>{{ $item->status  }}</td>
                                <td>{{ $item->actor->user_name  }}</td>
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
            {{$reports->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>

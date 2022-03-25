<div>
    <x-admin.form-control store="{{false}}" title="تخلفات"/>
    <div class="card card-custom">
        <div class="card-body">
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>گزارش دهنده</th>
                            <th>کاربر متخلف</th>
                            <th>شماره همراه</th>
                            <th>تاریخ</th>
                            <th>موضوع</th>
                            <th>توضیحات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($offends as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->user->phone }}</td>
                                <td>{{ $item->date }}</td>
                                <td>{{ $item->subject }}</td>
                                <td style="width: 40%;">{!!  $item->content !!}</td>
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
            {{$offends->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>

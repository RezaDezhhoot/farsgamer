<div>
    <x-admin.form-control store="{{false}}"  title="معاملات"/>
    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <button wire:click="$set('status', '')" class="btn btn-link"  title="">
                    همه
                    ({{$statusCount['all']}})
                </button>
                @foreach($data['status'] as $key => $item)
                    @if($status == $key)
                        <button type="button" wire:click="$set('status', '{{$key}}')" class="btn btn-{{$item['color']}} disabled"  title="">
                            {{$item['label']}} ({{$statusCount[$key]}})
                        </button>
                    @else
                        <button wire:click="$set('status', '{{$key}}')" class="btn btn-{{$item['color']}}" title="">
                            {{$item['label']}} ({{$statusCount[$key]}})
                        </button>
                    @endif
                @endforeach
            </div>
            <x-admin.forms.dropdown id="category" :data="$data['category']" label="دسته بندی" wire:model="category"/>
            <x-admin.forms.dropdown id="way" :data="$data['way']" label="روال" wire:model="way"/>
            @include('livewire.admin.layouts.advance-table')
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped" id="kt_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>کد معامله</th>
                            <th>اعضای معامله</th>
                            <th>اگهی</th>
                            <th>پیشرفت معامله</th>
                            <th>روال معامله</th>
                            <th>وضعیت</th>
                            <th>زمان باقی مانده</th>
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($transactions as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a>{{ $item->code }}</a>
                                </td>
                                <td>
                                    <ul class="list-inline">
                                        <li>
                                            <a title="{{$item->seller->name}}" href="{{route('admin.store.user',$item->seller->user_name)}}" target="_blank">
                                                فروشنده : {{$item->seller->user_name}}
                                            </a>
                                        </li>
                                        <li>
                                            <a title="{{$item->customer->name}}" href="{{route('admin.store.user',$item->customer->user_name)}}">
                                                خریدار : {{$item->customer->user_name}}
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <a href="{{route('admin.store.order',[''])}}">{{ $item->order->slug }}</a>
                                </td>
                                <td>
                                    <progress style="width: 100%;background: #fff"  max="100" value="{{ $item->progress }}"></progress>
                                    <br>
                                    <small>{{ $item->progress }}% کامل شده</small>
                                </td>
                                <td>
                                    {{ $item['is_returned'] == 0 ? 'عادی' : 'مرجوعی' }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link btn-xs">
                                        {{ $item->status_label }}
                                    </button>
                                </td>
                                <td dir="ltr">
                                    @if($item->time > 0)
                                        {{ $item->time }}
                                    @else
                                       اتمام زمان
                                    @endif
                                </td>
                                <td>
                                    {{ $item->date }}
                                </td>
                                <td class="nowrap">
                                    <a href="{{ route('admin.store.transaction',['edit',$item->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> مشاهده </a>
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

            {{$transactions->links('livewire.admin.layouts.paginate')}}
        </div>
    </div>
</div>


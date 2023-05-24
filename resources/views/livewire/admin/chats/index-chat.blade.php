<div>
    <x-admin.form-control store="{{false}}" title="چت ها" />
    <div class="d-flex flex-row">
        <!--begin::Aside-->
        <div class="flex-row-auto offcanvas-mobile w-350px w-xl-400px" id="kt_chat_aside" wire:ignore.self>
            <!--begin::Card-->
            <div class="card card-custom" wire:ignore.self>
                <!--begin::Body-->
                <div class="card-body" wire:ignore.self>
                    <!--begin:Search-->
                    <div class="input-group input-group-solid" wire:ignore.self>
                        <div class="input-group-prepend" wire:ignore.self>
                                                    <span class="input-group-text">
															<span class="svg-icon svg-icon-lg">
																<!--begin::Svg Icon | path:assets/media/svg/icons/General/Search.svg-->
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																		<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
																	</g>
																</svg>
                                                                <!--end::Svg Icon-->
															</span>
                                                    </span>
                        </div>
                        <input wire:model="search" type="text" class="form-control py-4 h-auto" placeholder=" نام کاربری - شماره - کد گفتوگو" />
                    </div>
                    <!--end:Search-->
                    <!--begin:Users-->
                    <div wire:ignore.self class="mt-7 scroll scroll-pull">
                        @foreach($groups as $group)
                            <!--begin:User-->
                                <div style="cursor: pointer;border: 1px solid aliceblue;border-radius: 5px;padding: 15px;" class="d-flex align-items-center justify-content-between mb-5" wire:click="openChatList('{{$group->id}}')">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol-group symbol-hover justify-content-center">
                                            <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="{{ $group->user_one->user_name }}">
                                                <img  src="{{ ($group->user_one->profile_image) }}" />
                                            </div>
                                            <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="{{ $group->user_two->user_name }}">
                                                <img  src="{{ ($group->user_two->profile_image) }}" />
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">
                                                {{ $group->user_one->user_name }},
                                                {{ $group->user_two->user_name }}
                                            </a>
                                            <span class="text-muted font-weight-bold font-size-sm">
                                               وضیعت :  {{ $group->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="text-muted font-weight-bold font-size-sm">
                                            {{ $group->last }}
                                        </span>
                                    </div>
                                </div>
                                <!--end:User-->
                        @endforeach
                    </div>
                    <!--end:Users-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Aside-->
        <!--begin::Content-->
        <div class="flex-row-fluid ml-lg-8" id="kt_chat_content" wire:ignore.self>
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Header-->

                <div class="card-header align-items-center px-4 py-3" wire:ignore.self>
                    <div class="text-left flex-grow-1">
                        <!--begin::Aside Mobile Toggle-->
                        <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md d-lg-none" id="kt_app_chat_toggle">
														<span class="svg-icon svg-icon-lg">
															<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Adress-book2.svg-->
															<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																	<rect x="0" y="0" width="24" height="24" />
																	<path d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z" fill="#000000" opacity="0.3" />
																	<path d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z" fill="#000000" />
																</g>
															</svg>
                                                            <!--end::Svg Icon-->
														</span>
                        </button>
                        <!--end::Aside Mobile Toggle-->
                        <!--begin::Dropdown Menu-->
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ki ki-bold-more-hor icon-md"></i>
                            </button>
                            <div class="dropdown-menu p-0 m-0 dropdown-menu-left dropdown-menu-md">
                                <!--begin::Navigation-->
                                <ul class="navi navi-hover py-5">
                                    @if(!empty($chatList))
                                        @if($chatList->status == $open)
                                            <li class="navi-item" style="cursor: pointer" wire:click="blockChat()">
                                                <a class="navi-link">
                                                                    <span class="navi-icon">
																			<i class="fas fa-lock"></i>
																		</span>
                                                    <span class="navi-text">بلاک کردن این گفتوگو</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if($chatList->status == $close)
                                            <li class="navi-item"  style="cursor: pointer" wire:click="unBlockChat()">
                                                <a class="navi-link">
                                                                        <span class="navi-icon">
                                                                                <i class="fas fa-lock-open"></i>
                                                                            </span>
                                                    <span class="navi-text">انبلاک کردن این گفتوگو</span>
                                                </a>
                                            </li>
                                        @endif
                                        <li class="navi-item"  style="cursor: pointer" onclick="deleteChat()">
                                            <a class="navi-link">
                                                                    <span class="navi-icon">
																			<i class="flaticon-delete"></i>
																		</span>
                                                <span class="navi-text">حذف این گفتوگو</span>
                                            </a>
                                        </li>
                                    @endif
                                        <li class="navi-item"  style="cursor: pointer" wire:click="render()">
                                            <a class="navi-link">
                                                                    <span class="navi-icon">
																			<i class="flaticon2-reload"></i>
																		</span>
                                                <span class="navi-text">رفرش کردن</span>
                                            </a>
                                        </li>
                                </ul>
                                <!--end::Navigation-->
                            </div>
                        </div>
                        <!--end::Dropdown Menu-->
                    </div>
                    <div class="symbol-group symbol-hover justify-content-center">
                        <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip">
                            @if(!is_null($chatList) && !empty($chatList))
                                وضیعت :  {{ $chatList->statusLabel }}
                            @endif
                        </div>
                    </div>
                    <div class="text-center text-center">
                        <div class="symbol-group symbol-hover justify-content-center">
                            @if(!is_null($chatList) && !empty($chatList))
                                <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="{{ ($chatList->user_one->user_name) }}">
                                    <img src="{{ ($chatList->user_one->profile_image) }}" />
                                </div>
                                <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="{{ ($chatList->user_two->user_name) }}">
                                    <img  src="{{ ($chatList->user_two->profile_image) }}" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body" wire:ignore.self>
                    <!--begin::Scroll-->
                    <div class="scroll scroll-pull" data-mobile-height="350" wire:ignore.self>
                        <!--begin::Messages-->
                        <div class="messages">
                            @if(!is_null($chatList) && !empty($chatList))
                                @foreach($chatList->chats as $item)
                                    @if($item->sender_id == auth()->id())
                                        <div class="d-flex flex-column mb-5 align-items-start">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-35 mr-3">
                                                <img  src="{{ (auth()->user()->profile_image) }}" />
                                            </div>
                                            <div>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">شما</a>
                                                <span class="text-muted font-size-sm">
                                                     {{ $item->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                                            {{ $item->content }}
                                        </div>
                                    </div>
                                    @else
                                        <div class="d-flex flex-column mb-5 align-items-end">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span class="text-muted font-size-sm">{{ $item->created_at->diffForHumans() }}</span>
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">
                                                    {{ $item->sender->user_name }}
                                                </a>
                                            </div>
                                            <div class="symbol symbol-circle symbol-35 ml-3">
                                                <img  src="{{ $item->sender->profile_image }}" />
                                            </div>
                                        </div>
                                        <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">
                                            {{ $item->content }}
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <!--end::Messages-->
                    </div>
                    <!--end::Scroll-->
                </div>
                <!--end::Body-->
                <!--begin::Footer-->
                <div wire:ignore.self class="card-footer align-items-center">
                    <!--begin::Compose-->
                    <textarea wire:model.defer="chatText" wire:keydown.enter="sendChatText" class="form-control border-0 p-0" rows="2" placeholder="پیامی بنویسید"></textarea>
                    <div class="d-flex align-items-center justify-content-between mt-5">
                        <div class="mr-3">
                        </div>
                        <div>
                            <button wire:click="sendChatText()" type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6">ارسال</button>
                        </div>
                    </div>
                    <!--begin::Compose-->
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Content-->
    </div>
</div>
@push('scripts')
    <script>
        function deleteChat() {
            Swal.fire({
                title: 'حذف گفتوگو!',
                text: 'آیا از حذف این گفتوگو اطمینان دارید؟',
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
                            'گفتوگو مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteChat', )
                }
            })
        }
    </script>
@endpush















































{{--<div>--}}
{{--    <x-admin.title-component title="گفتوگو ها"/>--}}
{{--    <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت" wire:model="status"/>--}}
{{--    <div class="x_content">--}}
{{--        <div id="datatable-responsive_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">--}}
{{--            @include('livewire.admin.layouts.advance-table')--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-12">--}}
{{--                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive" cellspacing="0">--}}
{{--                        <thead>--}}
{{--                        <tr>--}}
{{--                            <th>کاربر</th>--}}
{{--                            <th>کاربر</th>--}}
{{--                            <th>وضعیت</th>--}}
{{--                            <th>عملیات</th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        @forelse($groups as $item)--}}
{{--                            <tr>--}}
{{--                                <td>{{ $item->user_one->user_name }}</td>--}}
{{--                                <td>{{ $item->user_two->user_name }}</td>--}}
{{--                                <td>{{ $item::getStatus()[$item->status] }}</td>--}}
{{--                                <td>--}}
{{--                                    <a href="{{ route('admin.store.chat',['edit', $item->id]) }}" class="btn btn-info btn-xs">--}}
{{--                                        <i class="fa fa-pencil"></i> ویرایش--}}
{{--                                    </a>--}}
{{--                                    <button onclick="deleteItem({{$item->id}})" class="btn btn-danger btn-xs">--}}
{{--                                        <i class="fa fa-trash-o"></i> حذف--}}
{{--                                    </button>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @empty--}}
{{--                            <td class="text-center" colspan="6">--}}
{{--                                دیتایی جهت نمایش وجود ندارد--}}
{{--                            </td>--}}
{{--                        @endforelse--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            {{$groups->links('livewire.admin.layouts.paginate')}}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--@push('scripts')--}}
{{--    <script>--}}
{{--        function deleteItem(id) {--}}
{{--            Swal.fire({--}}
{{--                title: 'حذف گفتوگو!',--}}
{{--                text: 'آیا از حذف این گفتوگو اطمینان دارید؟',--}}
{{--                icon: 'warning',--}}
{{--                showCancelButton: true,--}}
{{--                confirmButtonColor: '#3085d6',--}}
{{--                cancelButtonColor: '#d33',--}}
{{--                cancelButtonText: 'خیر',--}}
{{--                confirmButtonText: 'بله'--}}
{{--            }).then((result) => {--}}
{{--                if (result.value) {--}}
{{--                    if (result.isConfirmed) {--}}
{{--                        Swal.fire(--}}
{{--                            'موفیت امیز!',--}}
{{--                            'گفتوگو مورد نظر با موفقیت حذف شد',--}}
{{--                        )--}}
{{--                    }--}}
{{--                @this.call('delete', id)--}}
{{--                }--}}
{{--            })--}}
{{--        }--}}
{{--    </script>--}}
{{--@endpush--}}
